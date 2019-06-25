<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 14/04/2016
 * Time: 21:17
 */

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Person;
use AppBundle\Entity\Transfer;
use AppBundle\Entity\AgencePartner;
use AppBundle\Form\Front\InterVilleTransfer\InterVilleTransferFirstStepType;
use AppBundle\Form\PersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class InterVilleTransfer
 * @package AppBundle\Controller\Front
 * @Route("/reservation-navette-inter-villes")
 */
class InterVilleTransferController extends Controller
{


    // Solution temporaire avant d'ajouter les roles.
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/first_step_nr",name="interVille_transfer_first_stepnr")
     */
    public function firstStepNrAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(InterVilleTransferFirstStepType::class, $transfer);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                if ($form->get('date')->getData() instanceof \DateTime) {
                    $date1 = $form->get('date')->getData()->format('Y-m-d ') . $form->get('time')->getData();

                    $transfer->setPickupDate(date_create_from_format('Y-m-d H:i', $date1));
                }

                $transfer->setType(Transfer::INTERVILLE_TRANSFERT_To_TOWN);
                $transfer->setStatus(Transfer::STATUS_OPEN);

                $price = $this->get('transfer.interville.service')->calculateTarif($transfer, true);

                $transfer->setPrice($price["prix"]);
                $transfer->setAddress($price["rdv"]);

                $sessionTransferToken = md5(time());
                $this->get('session')->set($sessionTransferToken, $transfer);
                $this->get('session')->set('transfer_timestamp_' . $sessionTransferToken, time());
                return $this->redirectToRoute('interVille_transfer_second_step', array('token' => $sessionTransferToken));
            } else {

            }
        }

        return $this->render("@App/front/interVilleTransfer/first_step_nr.html.twig", array(
            'form' => $form->createView()
        ));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/first_step",name="interVille_transfer_first_step")
     */
    public function firstStepAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(InterVilleTransferFirstStepType::class, $transfer);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                if ($form->get('date')->getData() instanceof \DateTime) {
                    $date1 = $form->get('date')->getData()->format('Y-m-d ') . $form->get('time')->getData();

                    $transfer->setPickupDate(date_create_from_format('Y-m-d H:i', $date1));
                }

                $transfer->setType(Transfer::INTERVILLE_TRANSFERT_To_TOWN);
                $transfer->setStatus(Transfer::STATUS_OPEN);

                if ($this->getUser() != null) {
                    $usertype=$this->getUser()->getType();
                }
                else {
                    $usertype = "customer";
                }

                $price = $this->get('transfer.interville.service')->calculateTarif($transfer, $usertype);

                $transfer->setPrice($price["prix"]);
                $transfer->setAddress($price["rdv"]);

                $sessionTransferToken = md5(time());
                $this->get('session')->set($sessionTransferToken, $transfer);
                $this->get('session')->set('transfer_timestamp_' . $sessionTransferToken, time());
                return $this->redirectToRoute('interVille_transfer_second_step', array('token' => $sessionTransferToken));
            } else {

            }
        }

        return $this->render("@App/front/interVilleTransfer/first_step.html.twig", array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/calculate_tarif_interville",name="calculate_tarif_inter_ville",options={"expose"=true})
     * @Method("POST")
     */
    public function calculateTarifAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(InterVilleTransferFirstStepType::class, $transfer);
        $form->handleRequest($request);

        if ($this->getUser() != null) {
            $usertype=$this->getUser()->getType();
        }
        else {
            $usertype = "customer";
        }

        $tarif = $this->get('transfer.interville.service')->calculateTarif($transfer,$usertype);
        return new JsonResponse($tarif);
    }

    /**
     * @param Request $request
     * @param srting
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/second_step/{token}",name="interVille_transfer_second_step")
     */
    public function secondStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('interVille_transfer_first_step');
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
                return $this->redirectToRoute('interVille_transfer_third_step', array('token' => $token));
            } else {

            }
        }

        return $this->render("@App/front/interVilleTransfer/second_step.html.twig", array(
            'transfer' => $transfer,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/third_step/{token}",name="interVille_transfer_third_step")
     */
    public function thridStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('interVille_transfer_first_step');
        }

        $transfer = $this->get('session')->get($token);
        return $this->render("@App/front/interVilleTransfer/third_step.html.twig",
            array('transfer' => $transfer,
                'token' => $token));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/fourth_step/{token}",name="interVille_transfer_fourth_step")
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

            //ajouter le partenaire s'il existe
            $transfer->setPartner($this->get('source.service')->getSourcePartner());

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

        $relayCustomers = $this->getDoctrine()
            ->getRepository("AppBundle:RelayCustomerDetail")
            ->findAll();

        return $this->render("@App/front/common/fourth_step.html.twig",
            array('token' => $token,
                'transfer' => $transfer,
                'relayCustomers' => $relayCustomers,
                'partnerAgencies' => $partnerAgencies
            ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/available_town_list",name="available_town_list")
     */
    public function availableTownList()
    {
        $list = $this->getDoctrine()->getRepository("AppBundle:InterVillePrices")
            ->getAvailableTownList();

        return $this->render("@App/front/interVilleTransfer/available_town_list.html.twig", array(
            'list' => $list
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/tarifsIndex",name="liste_tarifs_interville")
     */
    public function tarifsIndexAction()
    {

        return $this->render("@App/front/interVilleTransfer/tarifsIndex.html.twig");
    }

}
