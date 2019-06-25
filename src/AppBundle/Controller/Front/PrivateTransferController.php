<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 14/04/2016
 * Time: 21:17
 */

namespace AppBundle\Controller\Front;

use AppBundle\Entity\InterVillePrices;
use AppBundle\Entity\Person;
use AppBundle\Entity\Transfer;
use AppBundle\Entity\AgencePartner;
use AppBundle\Form\Front\PrivateTransfer\PrivateTransferFirstStepType;
use AppBundle\Form\PersonType;
use AppBundle\Service\Validator\Front\DateAllerRetourConstraint\DateAllerConstraint;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PrivateTransfer
 * @package AppBundle\Controller\Front
 * @Route("/reservation-transfert-aeroport-domicile")
 */
class PrivateTransferController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/first_step",name="private_transfer_first_step")
     */
    public function firstStepAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(PrivateTransferFirstStepType::class, $transfer);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                if ($transfer->getRoundTrip()) {
                    $transfer->setLocation2($transfer->getLocation());
                }

                if ($form->get('date')->getData() instanceof \DateTime) {
                    $date1 = $form->get('date')->getData()->format('Y-m-d ') . $form->get('time')->getData();
                    $transfer->setPickupDate(date_create_from_format('Y-m-d H:i', $date1));
                }

                if ($form->get('date2')->getData() instanceof \DateTime) {
                    $date2 = $form->get('date2')->getData()->format('Y-m-d ') . $form->get('time2')->getData();
                    $transfer->setPickupDate2(date_create_from_format('Y-m-d H:i', $date2));
                }


                $price = $this->get('transfer.private.service')->calculateTarif($transfer,true);
                $transfer->setPrice($price['price']);

                $valid = $this->get('validator')->validate($transfer,new DateAllerConstraint());
                if (count($valid) == 0){
                    $transfer->setType(Transfer::PRIVATE_TRANSFER_TO_TOWN);
                    $transfer->setStatus(Transfer::STATUS_OPEN);

                    $sessionTransferToken = md5(time());
                    $this->get('session')->set($sessionTransferToken, $transfer);
                    $this->get('session')->set('transfer_timestamp_' . $sessionTransferToken, time());
                    // var_dump($transfer);die(0);
                    return $this->redirectToRoute('private_transfer_second_step', array('token' => $sessionTransferToken));
                }
            } else {

            }
        }

        return $this->render("@App/front/privateTransfer/first_step.html.twig", array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/calculate_tarif",name="calculate_tarif",options={"expose"=true})
     * @Method("POST")
     */
    public function calculateTarifAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(PrivateTransferFirstStepType::class, $transfer);
        $form->handleRequest($request);

        if ($form->get('date')->getData() instanceof \DateTime) {
            $date1 = $form->get('date')->getData()->format('Y-m-d ') . $form->get('time')->getData();
            $transfer->setPickupDate(date_create_from_format('Y-m-d H:i', $date1));
        }

        if ($form->get('date2')->getData() instanceof \DateTime) {
            $date2 = $form->get('date2')->getData()->format('Y-m-d ') . $form->get('time2')->getData();
            $transfer->setPickupDate2(date_create_from_format('Y-m-d H:i', $date2));
        }

        if ($transfer->getRoundTrip()) {
            $transfer->setLocation2($transfer->getLocation());
        }
        $tarif = $this->get('transfer.private.service')->calculateTarif($transfer, true);

        return new JsonResponse($tarif);
    }

    /**
     * @param Request $request
     * @param srting
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/second_step/{token}",name="private_transfer_second_step")
     */
    public function secondStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('private_transfer_first_step');
        }

        $transfer = $this->get('session')->get($token);

        if ($transfer->getPassenger()) {
            $passenger = $transfer->getPassenger();
        } else {
            $passenger = new Person();
        }


        $form = $this->createForm(PersonType::class, $passenger);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $transfer->setPassenger($passenger);
                return $this->redirectToRoute('private_transfer_third_step', array('token' => $token));
            } else {

            }
        }

        return $this->render("@App/front/privateTransfer/second_step.html.twig", array(
            'transfer' => $transfer,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/third_step/{token}",name="private_transfer_third_step")
     */
    public function thridStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('private_transfer_first_step');
        }

        $transfer = $this->get('session')->get($token);

        return $this->render("@App/front/privateTransfer/third_step.html.twig",
            array('transfer' => $transfer,
                'token' => $token));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/fourth_step/{token}",name="private_transfer_fourth_step")
     */
    public function fourthStepAction(Request $request, $token = null)
    {
        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('private_transfer_first_step');
        }

        $transfer = $this->get('session')->get($token);

        //Save if user is connected
        if ($this->getUser() != null) {
            if ($transfer->getId() != null) {
                $transfer = $this->getDoctrine()->getManager()->merge($transfer);
            }

            $transfer->setCreatedBy($this->getUser());
            if (in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles()))
                $transfer->setStatus(Transfer::STATUS_OPEN_B2B);
            else
                $transfer->setStatus(Transfer::STATUS_OPEN);

            //dump($transfer);die;
            $location = $this->getDoctrine()->getManager()->merge($transfer->getLocation());
            $transfer->setLocation($location);
            $flight = $this->getDoctrine()->getManager()->merge($transfer->getFlight());
            $transfer->setFlight($flight);


            $transfer->setPartner($this->get('source.service')->getSourcePartner());

            if ($transfer->getCreatedBy()->getAgencePartner()) {
                $prixHT = $transfer->getPrice() * 100 / 110;
                $commisionValue = ($prixHT * $transfer->getCreatedBy()->getAgencePartner()->getCommission()) / 100;
                $transfer->setCommission($commisionValue);
                $price = $this->get('transfer.private.service')->calculateTarif($transfer,true);
                $transfer->setPrice($price['price'] - $commisionValue);
            }

            if ($transfer->getRoundTrip()) {
                $location2 = $this->getDoctrine()->getManager()->merge($transfer->getLocation2());
                $transfer->setLocation2($location2);
                $flight2 = $this->getDoctrine()->getManager()->merge($transfer->getFlight2());
                $transfer->setFlight2($flight2);
            } else {
                $transfer->setLocation2(null);
                $transfer->setFlight2(null);
            }


            $this->getDoctrine()->getManager()->persist($transfer);
            $this->getDoctrine()->getManager()->flush();
            $transfer->initRef();
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->set($token, $transfer);

            //$this->get('transfer.common.service')->sendVoucher($transfer);
        }//User is not connected

        $partnerAgencies = $this->getDoctrine()
            ->getRepository("AppBundle:AgencePartner")
            ->findAll();

        return $this->render("@App/front/common/fourth_step.html.twig",
            array('token' => $token,
                'transfer' => $transfer,
                'partnerAgencies' => $partnerAgencies
            ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/datatable",name="datatable_exemple")
     */
    public function datatExempleAction(Request $request)
    {
        $interVillePrices = new InterVillePrices();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:InterVillePrices');
        $villes = $repo->findAll();
        // var_dump($villes);
        return $this->render("@App/front/privateTransfer/datatable.html.twig",
            array('villes' => $villes));
    }

}