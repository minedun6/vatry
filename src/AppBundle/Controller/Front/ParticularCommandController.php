<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Person;
use AppBundle\Form\PersonType;

/**
 * Class ParticularCommandController
 * @package AppBundle\Controller\Front
 * @Route("/commande-particuliere")
 */
class ParticularCommandController extends Controller
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/demande", name="send_particular_command")
     */
    public function sendParticularCommandAction(Request $request)
    {
        if ($request->getMethod() == "POST") {
            $flight_time = $request->get('flight_time') != null ? DateTime::createFromFormat("d/m/Y H:i", $request->get('flight_time')) : '';
            $departure_place = $request->get('departure_place') . ' ' . $request->get('zip_code') . ' ' . $request->get('ville');
            $arrival_place = $request->get('arrival_place') . ' ' . $request->get('zip_code1') . ' ' . $request->get('ville1');
            $client_phone_number = $request->get('phone_number');
            $client_name = $request->get('name');
            $client_email = $request->get('email');
            $adults_number = $request->get('adults_number');
            $infants_number = $request->get('infants_number');
            $babies_number = $request->get('babies_number');
            $comments_aller = nl2br($request->get('message-aller'));
            $comments_retour = nl2br($request->get('message-retour'));
            $round_trip = $request->get('round-trip') == 'on' ? true : false;
            $round_trip_date = $request->get('roundtrip-date') != null ? DateTime::createFromFormat("d/m/Y H:i", $request->get('roundtrip-date')) : '';
            $data = array(
                'flight_time' => $flight_time,
                'arrival_place' => $arrival_place,
                'departure_place' => $departure_place,
                'client_name' => $client_name,
                'phone_number' => $client_phone_number,
                'infants_number' => $infants_number,
                'adults_number' => $adults_number,
                'babies_number' => $babies_number,
                'comments_aller' => $comments_aller,
                'comments_retour' => $comments_retour,
                'round_trip' => $round_trip,
                'round_trip_date' => $round_trip_date,
                'email' => $client_email
            );

            $sended = $this->get('transfer.common.service')->sendParticularCommandRequestEmail($data);
            if ($sended)
                $this->get('session')->getFlashBag()->add('success', "Demande de devis a été envoyée avec succès");

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App\front\particularCommand\send_request.html.twig');
    }

    /**
     * @Route("/devis", name="devis_particular_command")
     */
    public function createDevis(Request $request)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if ($request->getMethod() == 'POST') {
            $flight_time = $request->get('flight_time') != null ? DateTime::createFromFormat("d/m/Y H:i", $request->get('flight_time')) : '';
            $departure_place = $request->get('departure_place') . ' ' . $request->get('zip_code') . ' ' . $request->get('ville');
            $arrival_place = $request->get('arrival_place') . ' ' . $request->get('zip_code1') . ' ' . $request->get('ville1');
            $client_email = $request->get('email');
            $adults_number = $request->get('adults_number');
            $infants_number = $request->get('infants_number');
            $babies_number = $request->get('babies_number');
            $tarif = doubleval($request->get('tarif'));
            $client_name = $request->get('name');
            $round_trip = $request->get('round-trip') == 'on' ? true : false;
            $round_trip_date = $request->get('roundtrip-date') != null ? DateTime::createFromFormat("d/m/Y H:i", $request->get('roundtrip-date')) : '';
            $names = explode(' ', $client_name);
            $person = new Person();
            $person->setCivility('M');
            $person->setLastname(isset($names[0]) ? $names[0] : '');
            $person->setName(isset($names[1]) ? $names[1] : '');
            $person->setEmail($client_email);
            $transfer = new Transfer();
            if ($this->getUser()) {
                $transfer->setCreatedBy($this->getUser());
            } else {
                return $this->redirectToRoute('devis_particular_command');
            }
            $transfer->setStatus(Transfer::STATUS_OPEN);
            $transfer->setType(Transfer::PARTICULAR_COMMAND);
            $transfer->setAddress($departure_place);
            $transfer->setAddress2($arrival_place);
            $transfer->setPickupDate($flight_time);
            if ($round_trip == true) {
                $transfer->setPickupDate2($round_trip_date);
                $transfer->setRoundTrip(true);
            }
            $transfer->setQty($adults_number);
            $transfer->setQtyBaby($babies_number);
            $transfer->setQtyChild($infants_number);
            $transfer->setPrice($tarif);
            $this->getDoctrine()->getManager()->persist($person);
            $transfer->setPassenger($person);
            $this->getDoctrine()->getManager()->persist($transfer);
            $this->getDoctrine()->getManager()->flush();
            $transfer->initRef();
            $this->getDoctrine()->getManager()->flush();

            $sended = $this->get('transfer.common.service')->sendParticularCommandResponseEmail($transfer, $client_email, $client_name);
            return $this->redirectToRoute('homepage');
        }

        return $this->render('@App/front/particularCommand/create_devis.html.twig', array(
            'parent' => $parent
        ));
    }

    /**
     * @param Transfer $transfer
     * @Route("/second-step/{transfer}", name="command_particular_second_step")
     * @return Response
     *
     */
    public function secondStepAction(Request $request, Transfer $transfer = null)
    {
        if (!$transfer || $transfer->getType() != Transfer::PARTICULAR_COMMAND) {
            return $this->redirectToRoute('send_particular_command');
        }

        if ($transfer->getStatus() != Transfer::STATUS_OPEN) {
            $this->get('session')->getFlashBag()->add('success', "La commande que vous deamandez n'est plus disponible");
            return $this->redirectToRoute('send_particular_command');
        }
        if ($transfer->getPassenger() != null) {
            $passenger = $transfer->getPassenger();
        } else {
            $passenger = new Person();
        }


        $form = $this->createForm(PersonType::class, $passenger);

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);
            if ($form->isValid()) {
                $transfer->setPassenger($passenger);
                $this->getDoctrine()->getManager()->persist($transfer);
                $this->getDoctrine()->getManager()->flush();
                if (1) {

                    return $this->redirectToRoute('command_particular_third_step', array(
                        'transfer' => $transfer->getId()
                    ));
                }

            }
        }

        return $this->render("@App/front/particularCommand/second_step.html.twig", array(
            'transfer' => $transfer,
            'form' => $form->createView()
        ));
    }


    /**
     * @param Transfer $transfer
     * @Route("/third-step/{transfer}", name="command_particular_third_step")
     * @return Response
     *
     */
    public function thirdStepAction(Transfer $transfer)
    {
        if (!$transfer || $transfer->getType() != Transfer::PARTICULAR_COMMAND) {
            return $this->redirectToRoute('send_particular_command');
        }

        if ($transfer->getStatus() != Transfer::STATUS_OPEN) {
            $this->get('session')->getFlashBag()->add('success', "La commande que vous deamandez n'est plus disponible");
            return $this->redirectToRoute('send_particular_command');
        }

        return $this->render("@App/front/particularCommand/third_step.html.twig", array(
            'transfer' => $transfer
        ));
    }

    /**
     * @param Transfer $transfer
     * @Route("/fourth-step/{transfer}", name="command_particular_fourth_step")
     * @return Response
     *
     */
    public function fourthStepAction(Transfer $transfer)
    {
        if (!$transfer || $transfer->getType() != Transfer::PARTICULAR_COMMAND) {
            return $this->redirectToRoute('send_particular_command');
        }

        if ($transfer->getStatus() != Transfer::STATUS_OPEN) {
            $this->get('session')->getFlashBag()->add('success', "La commande que vous deamandez n'est plus disponible");
            return $this->redirectToRoute('send_particular_command');
        }

        $partnerAgencies = $this->getDoctrine()
            ->getRepository("AppBundle:AgencePartner")
            ->findAll();
        return $this->render("@App/front/common/fourth_step.html.twig", array(
            'transfer' => $transfer,
            'partnerAgencies' => $partnerAgencies
        ));
    }

    /**
     * @return Response
     * @Route("/liste", name="particular_command_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $commands = $em->getRepository('AppBundle:Transfer')->findBy(array(
            'type' => Transfer::PARTICULAR_COMMAND,
            'status' => Transfer::STATUS_OPEN
        ));
        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render('@App/front/particularCommand/list.html.twig', array(
            'transfers' => $commands,
            'parent' => $parent
        ));
    }

    /**
     * @param Transfer|null $transfer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/cancel/{transfer}", name="particular_command_cancel")
     */
    public function cancelTransfer(Transfer $transfer = null)
    {
        if ($transfer && $transfer->getType() == Transfer::PARTICULAR_COMMAND) {
            if ($transfer->getStatus() == Transfer::STATUS_OPEN) {
                $transfer->setStatus(Transfer::STATUS_CANCEL);
                $this->getDoctrine()->getManager()->persist($transfer);
                $this->getDoctrine()->getManager()->flush();
                $sended = $this->get('transfer.common.service')->sendCancelEmail($transfer);
            }
        }

        return $this->redirectToRoute("particular_command_list");
    }

    /**
     * @param Request $request
     * @Route("/partner/demande", name="particular_command_agency")
     * @return Response
     */
    public function sendRequestFroPartnerAgency(Request $request)
    {
        $user = $this->getUser();
        if (!$user || !$user->getAgencePartner()) {
            return $this->redirectToRoute('homepage');
        }
        if ($request->getMethod() == "POST") {
            $flight_time = $request->get('flight_time') != null ? DateTime::createFromFormat("d/m/Y H:i", $request->get('flight_time')) : '';
            $departure_place = $request->get('departure_place') . ' ' . $request->get('zip_code') . ' ' . $request->get('ville');
            $arrival_place = $request->get('arrival_place') . ' ' . $request->get('zip_code1') . ' ' . $request->get('ville1');
            $adults_number = $request->get('adults_number');
            $infants_number = $request->get('infants_number');
            $babies_number = $request->get('babies_number');
            $comments_aller = nl2br($request->get('message-aller'));
            $comments_retour = nl2br($request->get('message-retour'));
            $round_trip = $request->get('round-trip') == 'on' ? true : false;
            $round_trip_date = $request->get('roundtrip-date') != null ? DateTime::createFromFormat("d/m/Y H:i", $request->get('roundtrip-date')) : '';
            $data = array(
                'flight_time' => $flight_time,
                'arrival_place' => $arrival_place,
                'departure_place' => $departure_place,
                'infants_number' => $infants_number,
                'adults_number' => $adults_number,
                'babies_number' => $babies_number,
                'comments_aller' => $comments_aller,
                'comments_retour' => $comments_retour,
                'round_trip' => $round_trip,
                'round_trip_date' => $round_trip_date,
            );

            $sended = $this->get('transfer.common.service')->sendParticularCommandRequestEmailForPartner($data, $user);
            if ($sended)
                $this->get('session')->getFlashBag()->add('success', "Demande de devis a été envoyée avec succès");

        }

        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render('@App/PartnerAgency/particular_command.html.twig', array(
            'parent' => $parent
        ));
    }

}