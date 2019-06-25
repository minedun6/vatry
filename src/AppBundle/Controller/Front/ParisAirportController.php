<?php

namespace AppBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Location;
use AppBundle\Entity\Transfer;
use AppBundle\Form\Front\ParisTransfer\TransferType;
use AppBundle\Service\Validator\Front\DateAllerRetourConstraint\DateAllerConstraint;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\PersonType;
use AppBundle\Entity\Person;

/**
 * Class ParisAirportController
 * @package AppBundle\Controller\Front
 * @Route("/reservation-navette-aeroport-paris")
 */
class ParisAirportController extends Controller
{
    /**
     * @Route("/first_step", name="paris_airport_first_step")
     */
    public function firstStepAction(Request $request)
    {
        $location = $this->getDoctrine()->getRepository("AppBundle:Location")
            ->findOneBy(array(
                "name" => "Paris 13",
                "zipCode" => "75013"
            ));

        $transfer = new Transfer();
        $transfer->setLocation($location);

        $form = $this->createForm(TransferType::class, $transfer);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $price = $this->get('paris_airport.transfer.service')->calculateTarif($transfer);

                $transfer->setPrice($price["prix"]);

                if ($form->get('date')->getData() instanceof \DateTime) {
                    $date1 = $this->get('paris_airport.transfer.service')->getPickupTime($transfer, $transfer->getDirection());
                    $transfer->setPickupDate($date1);
                }

                if ($transfer->getRoundTrip()) {
                    $transfer->setLocation2($transfer->getLocation());
                    $date2 = $this->get('paris_airport.transfer.service')->getPickupTime($transfer, $transfer->getDirection(), true);
                    $transfer->setPickupDate2($date2);
                } else {
                    $transfer->setLocation2(null);
                }
                $valid = $this->get('validator')->validate($transfer, new DateAllerConstraint());
                if (count($valid) == 0) {
                    $transfer->setType(Transfer::PARIS_AIRPORT);
                    $transfer->setStatus(Transfer::STATUS_OPEN);

                    $sessionTransferToken = md5(time());
                    $this->get('session')->set($sessionTransferToken, $transfer);
                    $this->get('session')->set('transfer_timestamp_' . $sessionTransferToken, time());

                    return $this->redirectToRoute('paris_airport_second_step', array('token' => $sessionTransferToken));
                }
            } else {

            }
        }

        return $this->render("@App/front/parisAirport/first_step.html.twig", array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param srting
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/second_step/{token}",name="paris_airport_second_step")
     */
    public function secondStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('paris_airport_first_step');
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
                return $this->redirectToRoute('paris_airport_third_step', array('token' => $token));
            } else {

            }
        }

        return $this->render("@App/front/parisAirport/second_step.html.twig", array(
            'transfer' => $transfer,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/third_step/{token}",name="paris_airport_third_step")
     */
    public function thridStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('paris_airport_first_step');
        }

        $transfer = $this->get('session')->get($token);

        return $this->render("@App/front/parisAirport/third_step.html.twig", array(
            'transfer' => $transfer,
            'token' => $token
        ));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/fourth_step/{token}",name="paris_airport_fourth_step")
     */
    public function fourthStepAction(Request $request, $token = null)
    {
        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('paris_airport_first_step');
        }

        $transfer = $this->get('session')->get($token);


        //Save if user is connected
        if ($this->getUser() != null) {
            if ($transfer->getId() != null) {
                $transfer = $this->getDoctrine()->getManager()->merge($transfer);
            }

            $transfer->setCreatedBy($this->getUser());
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

        // on peut gérer les cas B2B et B2C dans le fourth_step action des différents produits
        // en cas de B2B on les passent au fourth_step B2B donc il faut trouver un
        //moyen de détecter est ce que c'est du B2B ou du B2C pour les agences on teste sur le role et pour le commercial
        // on ajoute un 4éme bouton dans le twig du 4th step qui redirige vers le paiement B2B qui consisite donc à
        // seulement identifier l'agence  laquelle on va affecter le transfert en question et on lui envoi le lien
        return $this->render("@App/front/common/fourth_step.html.twig",
            array('token' => $token,
                'transfer' => $transfer,
                'partnerAgencies' => $partnerAgencies
            ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/calculate_tarif_paris_airport",name="calculate_tarif_paris_airport",options={"expose"=true})
     *
     */
    public function calculateTarifAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(TransferType::class, $transfer);
        $form->handleRequest($request);

        if ($transfer->getRoundTrip()) {
            $transfer->setLocation2($transfer->getLocation());
        }
        $tarif = $this->get('paris_airport.transfer.service')->calculateTarif($transfer);
        return new JsonResponse($tarif);
    }

}