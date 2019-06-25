<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 04/05/2016
 * Time: 19:44
 */

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Location;
use AppBundle\Entity\Person;
use AppBundle\Entity\Transfer;
use AppBundle\Entity\AgencePartner;
use AppBundle\Form\Front\Gare\TransferType;
use AppBundle\Form\PersonType;
use AppBundle\Service\Validator\Front\DateAllerRetourConstraint\DateAllerConstraint;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class GareTransferController
 * @package AppBundle\Controller\Front
 * @Route("/reservation-navette-aeroport-gare")
 */
class GareTransferController extends Controller
{


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/first_step/{zipCode}",name="gare_transfer_first_step")
     */
    public function firstStepAction(Request $request, $zipCode)
    {

        $location = $this->getDoctrine()->getRepository("AppBundle:Location")
            ->findOneBy(array(
                "zipCode" => $zipCode,
                "type" => Location::TYPE_GARE
            ));

        $transfer = new Transfer();
        $transfer->setLocation($location)
            ->setLocation2($location);

        $form = $this->createForm(TransferType::class, $transfer);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {

                if ($this->getUser() != null) {
                    $usertype=$this->getUser()->getType();
                }
                else {
                    $usertype = "customer";
                }

                $price = $this->get('gare.transfer.service')->calculateTarif($transfer, $usertype);

                $transfer->setPrice($price["prix"]);

                if ($form->get('date')->getData() instanceof \DateTime) {
                    $date1 = $this->get('gare.transfer.service')->getPickupTime($transfer, $transfer->getDirection());
                    $transfer->setPickupDate($date1);
                }

                if ($transfer->getRoundTrip()) {
                    $transfer->setLocation2($transfer->getLocation());
                    $date2 = $this->get('gare.transfer.service')->getPickupTime($transfer, $transfer->getDirection(), true);
                    $transfer->setPickupDate2($date2);
                } else {
                    $transfer->setLocation2(null);
                }
                $valid = $this->get('validator')->validate($transfer,new DateAllerConstraint());
                if (count($valid) == 0) {
                    $transfer->setType(Transfer::GARE_TRANSFER);
                    $transfer->setStatus(Transfer::STATUS_OPEN);

                    $sessionTransferToken = md5(time());
                    $this->get('session')->set($sessionTransferToken, $transfer);
                    $this->get('session')->set('transfer_timestamp_' . $sessionTransferToken, time());

                    return $this->redirectToRoute('gare_transfer_second_step', array('token' => $sessionTransferToken));
                }
            } else {

            }
        }

        return $this->render("@App/front/gareTransfer/first_step.html.twig", array(
            'form' => $form->createView(),
            "zipCode" => $zipCode,
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/calculate_tarif_gare",name="calculate_tarif_gare",options={"expose"=true})
     * @Method("POST")
     */
    public function calculateTarifAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(TransferType::class, $transfer);
        $form->handleRequest($request);
        if ($transfer->getRoundTrip()) {
            $transfer->setLocation2($transfer->getLocation());
        }
        if ($this->getUser() != null) {
            $usertype=$this->getUser()->getType();
        }
        else {
            $usertype = "customer";
        }
        $tarif = $this->get('gare.transfer.service')->calculateTarif($transfer, $usertype);
        return new JsonResponse($tarif);
    }

    /**
     * @param Request $request
     * @param srting
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/second_step/{token}",name="gare_transfer_second_step")
     */
    public function secondStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('gare_transfer_first_step');
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
                return $this->redirectToRoute('gare_transfer_third_step', array('token' => $token));
            } else {

            }
        }

        return $this->render("@App/front/gareTransfer/second_step.html.twig", array(
            'transfer' => $transfer,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/third_step/{token}",name="gare_transfer_third_step")
     */
    public function thridStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('gare_transfer_first_step',array('zipCode'=>'g1'));
        }

        $transfer = $this->get('session')->get($token);

        return $this->render("@App/front/gareTransfer/third_step.html.twig",
            array('transfer' => $transfer,
                'token' => $token));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/fourth_step/{token}",name="gare_transfer_fourth_step")
     */
    public function fourthStepAction(Request $request, $token = null)
    {
        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('gare_transfer_first_step');
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/gare_description/{gare}",name="gare_description",options={"expose"=true})
     */
    public function descriptionAction(Request $request,$gare = null){

        if ($request->isXmlHttpRequest()){


            if (!$request->request->has('date') || ! \DateTime::createFromFormat('d/m/Y',$request->request->get('date')) ){
                return new JsonResponse([]);
            }

            $date = \DateTime::createFromFormat('d/m/Y',$request->request->get('date'));



            $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")->getDestinationsByDate($date->format('Y-m-d'));

            $data = [];
            foreach($flights as $f){

                $depTime = Helpers::createDateTimeByMinutesV2($f->getTime(),-100);

                if ($gare == 'chalon'){
                    $arrivalTime = Helpers::createDateTimeByMinutesV2($f->getTime(),-140);
                }else{
                    $arrivalTime = Helpers::createDateTimeByMinutesV2($f->getTime(),-190);
                }

                if (  ($f->getFromLocation()=='vatry' && (strtotime($f->getTime()->format('H:i'))>= strtotime('10:30') ) && (strtotime($f->getTime()->format('H:i'))<= strtotime('22:30') ) )  ){

                $data[]= array(
                    'departure' => $arrivalTime->format('H:i'),
                    'arrival' =>  $depTime->format('H:i'),
                    'liftOff' => $f->getTime()->format('H:i'),
                    'destination' => $f->getToLocation(),
                    'numVol' => $f->getNum(),
                );
                }
            }
            return new JsonResponse($data);

        }
        $g=null;

        if ($gare == 'chalon'){
            $g="g1";
        }else{
            $g="g2";
        }

        $interville = $this->getDoctrine()->getRepository("AppBundle:InterVillePrices")->findOneBy(array(
            "zipCode" => $g
        ));

        $price = $interville->getAdultePrice();

        if ($gare == 'chalon'){
            return $this->render("@App/front/gareTransfer/gare_chalon.html.twig",array(
                'price'=> $price
            ));
        }else{
            return $this->render("@App/front/gareTransfer/gare_reims.html.twig",array(
                'price'=> $price
            ));
        }

    }

}