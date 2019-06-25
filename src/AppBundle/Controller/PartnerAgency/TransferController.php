<?php

namespace AppBundle\Controller\PartnerAgency;


use AppBundle\Entity\AgencePartner;
use AppBundle\Entity\User;
use AppBundle\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Form\ModifType;


/**
 * Class Transfer
 * @package AppBundle\Controller\PartnerAgency
 * @Route("/part_agency/gestion_transfer")
 */
class TransferController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/allTransfers/{type}/{affected}",name="agency_all_transfers")
     */
    public function listTransferAction(Request $request, $type=null, $affected=null)
    {
        //die($affected);
        $isLastMinute = $affected ? true : false;
        $user = $this->getUser();

        $condition2 = array('createdBy' => $user,'affectedTo' => $user);

        if($affected)
            $condition2 = array('affectedTo' => $user);

        if($type=="paye"){
            $status=array(Transfer::STATUS_PAID_B2B);
            /*$condition1 = array('status' => $status);

            $conditions = array_merge($condition1, $condition2);

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                $conditions,
                array('createdAt' => 'desc'),
                null,
                null);*/

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }
        else if($type=="annule"){
            $status=array(Transfer::STATUS_CANCEL_B2B);
            /*$condition1 = array('status' => $status);
            $conditions = array_merge($condition1, $condition2);

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null); */

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }
        else if ($type=="valid") {
            $status=array(Transfer::STATUS_VALID_B2B );
            /*$condition1 = array('status' => $status);
            $conditions = array_merge($condition1, $condition2);
            $transfers=$this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                $conditions,
                array('createdAt' => 'desc'),
                null,
                null);*/
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }else if($type=="wait"){
            $status=array(Transfer::STATUS_WAIT_B2B, Transfer::STATUS_OPEN_B2B );
            /*$condition1 = array('status' => $status);
            $conditions = array_merge($condition1, $condition2);

            $transfers=$this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                $conditions,
                array('createdAt' => 'desc'),
                null,
                null);*/
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }else if($type=="open"){
            $status=array(Transfer::STATUS_OPEN_B2B );
            /*$condition1 = array('status' => $status);
            $conditions = array_merge($condition1, $condition2);

            $transfers=$this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                $conditions,
                array('createdAt' => 'desc'),
                null,
                null);*/
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }else if($type=="all"){
            $status=array(Transfer::STATUS_PAID_B2B,Transfer::STATUS_CANCEL_B2B,Transfer::STATUS_VALID_B2B,Transfer::STATUS_WAIT_B2B,Transfer::STATUS_OPEN_B2B  );
            /*$condition1 = array('status' => $status);
            $conditions = array_merge($condition1, $condition2);

            $transfers=$this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                $conditions,
                array('createdAt' => 'desc'),
                null,
                null);*/

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
//            var_dump($transfers);die;
        }
        /*        foreach ($transfers as $t)
                {
                    var_dump($t->getPickupDate());
                    var_dump($t->getPickupDate2());
                    var_dump($t->getId());
                }
                die;
        */
        return $this->render("@App/PartnerAgency/Transfers/listeTransferts.html.twig", array(
            'transfers' => $transfers,
            'isLastMinute' => $isLastMinute
        ));
    }






    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/allTransfersArchives/{type}/{affected}",name="agency_all_transfers_archives")
     */
    public function listTransferArchivAction(Request $request, $type=null, $affected=null)
    {
        $isLastMinute = $affected ? true : false;
        $user = $this->getUser();
        $condition2 = array('createdBy' => $user);

        if($affected)
            $condition2 = array('affectedTo' => $user);

        if($type=="paye"){
            $status=array(Transfer::STATUS_PAID_B2B);
            /*$condition1 = array('status' => $status);
            $conditions = array_merge($condition1, $condition2);

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                $conditions,
                array('createdAt' => 'desc'),
                null,
                null);*/

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }
        else if($type=="annule"){
            $status=array(Transfer::STATUS_CANCEL_B2B);
            /*$condition1 = array('status' => $status);
            $conditions = array_merge($condition1, $condition2);

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                $conditions,
                array('createdAt' => 'desc'),
                null,
                null);*/
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }
        else if ($type=="valid") {
            $status=array(Transfer::STATUS_VALID_B2B );
            //    $condition1 = array('status' => $status);
            //   $conditions = array_merge($condition1, $condition2);

            /*            $transfers=$this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                            $conditions,
                            array('createdAt' => 'desc'),
                            null,
                            null);*/
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);

        }else if($type=="wait"){
            $status=array(Transfer::STATUS_WAIT_B2B );
            /*            $condition1 = array('status' => $status);
                        $conditions = array_merge($condition1, $condition2);

                        $transfers=$this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                            $conditions,
                            array('createdAt' => 'desc'),
                            null,
                            null);*/

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);

        }else if($type=="all"){

            $status=array(Transfer::STATUS_PAID_B2B,Transfer::STATUS_CANCEL_B2B,Transfer::STATUS_VALID_B2B,Transfer::STATUS_WAIT_B2B );

            /*            $condition1 = array('status' => $status);
                        $conditions = array_merge($condition1, $condition2);

                        $transfers=$this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                            $conditions,
                            array('createdAt' => 'desc'),
                            null,
                            null);*/

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findPartnerAgencyB2B($user, $status, $affected);
        }

        return $this->render("@App/PartnerAgency/Transfers/listeTransfertsArchives.html.twig", array(
            'transfers' => $transfers,
            'isLastMinute' => $isLastMinute
        ));
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modifier_passenger/{transfer}", name="agency_modif_passenger")
     */
    public function modifPassengerTransferAction(Request $request, Transfer $transfer=null){


        if ($transfer!=null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ||in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) ||in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) ||in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser() == $transfer->getCreatedBy() || $this->getUser() == $transfer->getAffectedTo())) {


            $form = $this->createForm(ModifType::class, $transfer);

            if(!$form->get('roundTrip')->getData())
            {
                $form->remove('pickupDate2');
                $form->remove('flight2');
            }


            $adminUserForm = $form->get('passenger');

            $adminUserForm->remove('zipCode');
            $adminUserForm->remove('country');

            $form->remove('pickupDate');
            $form->remove('pickupDate2');
            $form->remove('flight');
            $form->remove('flight2');
            $form->remove('status');

            $form->remove('roundTrip');
            $form->remove('remarques');
            $form->remove('payment');
            $form->remove('address');
            $form->remove('address2');
            $form->remove('location');
            $form->remove('location2');



            if ($request->getMethod() == 'GET'){
                return $this->render('@App/PartnerAgency/Transfers/modifie.html.twig',array('form'=>$form->createView(), 'transfer'=>$transfer));
            }
            else {
                $form->handleRequest($request);

                $this->getDoctrine()->getManager()->flush();

                $this->get('session')->getFlashBag()->add('success',"Modifcation de la commande réussie");

                return $this->render("@App/PartnerAgency/Transfers/transfer_details.html.twig",array(
                    'transfer' => $transfer
                ));

            }
        }
        else{
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/details/{transfer}",name="agency_details_transfer")
     */
    public function detailsTransferAction(Transfer $transfer=null){
        if ($transfer!=null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ||in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) ||in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) ||in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser() == $transfer->getCreatedBy() || $this->getUser() == $transfer->getAffectedTo())) {

            return $this->render("@App/PartnerAgency/Transfers/transfer_details.html.twig", array(
                'transfer' => $transfer
            ));
        }
        else{
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/persondetails/{id}", name="agency_transfer_person_details")
     */
    public function transferPersonDetails(Transfer $transfer){
        if ($transfer!=null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ||in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) ||in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) ||in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser() == $transfer->getCreatedBy() || $this->getUser() == $transfer->getAffectedTo())) {

            return $this->render("@App/PartnerAgency/Transfers/transfer_person_details.html.twig", array('transfer'=> $transfer));
        }
        else{
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }
    }

    private function log($msg,$level = 'INFO'){
        file_put_contents($this->container->getParameter('kernel.logs_dir')."/payment_interface.log",$level." LOG PAYMENT ".date('dmYHis').$msg."\n",FILE_APPEND);
    }

}
