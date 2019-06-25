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
use AppBundle\Form\Front\PorteAPorteTransfer\PorteAPorteTransferFirstStepType;
use AppBundle\Form\PersonType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PorteAPorteTransfer
 * @package AppBundle\Controller\Front
 * @Route("/reservation-transfert-partage-aeroport-domicile")
 */
class PorteAPorteTransferController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/first_step/{type}",name="porteAporte_transfer_first_step")
     */
    public function firstStepAction(Request $request, $type=null)
    {
        $transfer = new Transfer();
        $form = $this->createForm(PorteAPorteTransferFirstStepType::class, $transfer);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                //modifier les service
                if ($this->getUser() != null) {
                    $usertype=$this->getUser()->getType();
                }

                else {
                    $usertype = "customer";
                }
                $price = $this->get('transfer.porteaporte.service')->calculateTarif($transfer, $usertype);

                $transfer->setPrice($price["prix"]);

                if ($form->get('date')->getData() instanceof \DateTime) {
                    $date1 = $form->get('date')->getData()->format('Y-m-d ') . $form->get('time')->getData();
                    $transfer->setPickupDate(date_create_from_format('Y-m-d H:i', $date1));
                }


                $transfer->setType(Transfer::PORTEAPORTE_TRANSFERT_To_TOWN);
                $transfer->setStatus(Transfer::STATUS_OPEN);

                $sessionTransferToken = md5(time());
                $this->get('session')->set($sessionTransferToken, $transfer);
                $this->get('session')->set('transfer_timestamp_' . $sessionTransferToken, time());
                return $this->redirectToRoute('porteAporte_transfer_second_step', array('token' => $sessionTransferToken));
            } else {

            }
        }

        return $this->render("@App/front/porteAporteTransfer/first_step.html.twig", array(
            'form' => $form->createView(),
            'type' => $type
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/calculate_tarif_porteaporte",name="calculate_tarif_porte_a_porte",options={"expose"=true})
     * @Method("POST")
     */
    public function calculateTarifAction(Request $request)
    {
        $transfer = new Transfer();
        $form = $this->createForm(PorteAPorteTransferFirstStepType::class, $transfer);
        $form->handleRequest($request);
        // To Do ajouter le service de calcul des prix
        
        if ($this->getUser() != null) {
        $usertype=$this->getUser()->getType();
        }

        else {
        $usertype = "customer";
        }

        $tarif = $this->get('transfer.porteaporte.service')->calculateTarif($transfer,$usertype);

        return new JsonResponse($tarif);
    }




    /**
     * @param Request $request
     * @param srting
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/second_step/{token}",name="porteAporte_transfer_second_step")
     */
    public function secondStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('porteAporte_transfer_first_step');
        }

        $transfer = $this->get('session')->get($token);

        if ($transfer->getPassenger()){
            $passenger = $transfer->getPassenger();
        }else{
            $passenger = new Person();
        }


        $form = $this->createForm(PersonType::class, $passenger);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $transfer->setPassenger($passenger);
                return $this->redirectToRoute('porteAporte_transfer_third_step', array('token' => $token));
            } else {

            }
        }

        return $this->render("@App/front/porteAporteTransfer/second_step.html.twig", array(
            'transfer' => $transfer,
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/third_step/{token}",name="porteAporte_transfer_third_step")
     */
    public function thridStepAction(Request $request, $token = null)
    {

        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('porteAporte_transfer_first_step');
        }

        $transfer = $this->get('session')->get($token);

        return $this->render("@App/front/porteAporteTransfer/third_step.html.twig",
            array('transfer' => $transfer,
                'token' => $token));
    }

    /**
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/fourth_step/{token}",name="porteAporte_transfer_fourth_step")
     */
    public function fourthStepAction(Request $request, $token = null)
    {
        if (!$token || !$this->get('session')->has($token)) {
            return $this->redirectToRoute('private_transfer_first_step');
        }

        $transfer = $this->get('session')->get($token);


        //Save if user is connected
        if ($this->getUser() != null){
            if ($transfer->getId() != null){
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


            if ($transfer->getRoundTrip()){
                $location2 = $this->getDoctrine()->getManager()->merge($transfer->getLocation2());
                $transfer->setLocation2($location2);
                $flight2 = $this->getDoctrine()->getManager()->merge($transfer->getFlight2());
                $transfer->setFlight2($flight2);
            }else{
                $transfer->setLocation2(null);
                $transfer->setFlight2(null);
            }

            $this->getDoctrine()->getManager()->persist($transfer);
            $this->getDoctrine()->getManager()->flush();



 /*         $this->get('session')->set($token,$transfer);
            $transfer->initRef();
            $this->getDoctrine()->getManager()->flush();
 */

            $transfer->initRef();
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->set($token,$transfer);

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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/available_town_list",name="available_aggro_list")
     */
    public function availableTownList(){
        $list = $this->getDoctrine()->getRepository("AppBundle:PorteAPortePrice")
            ->getAvailableTownList();

        $aggroList = $this->getDoctrine()->getRepository("AppBundle:Agglomeration")->findBy([],['name'=> 'ASC']);

        return $this->render("@App/front/porteAporteTransfer/available_town_list.html.twig",array(
            'list' => $list,
            'aggro' => $aggroList
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/verify_flight_porte_a_porte",name="verify_flight_porte_a_porte",options={"expose"=true})
     * @Method("POST")
     */
    public function verifyFlightAction(Request $request)
    {
        if($request->get('flight') != 'Nice'){
            $usr= $this->getUser();
            if(!$usr || in_array('ROLE_CUSTOMER',$this->getUser()->getRoles())){
                return new JsonResponse(['granted'=>false]);
            }
        }
        return new JsonResponse(['granted'=>true]);
    }
}
