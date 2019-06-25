<?php

namespace AppBundle\Controller\Agent;


use AppBundle\Entity\Partner;
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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class Transfer
 * @package AppBundle\Controller\Agent
 * @Route("/agent/gestion_transfer")
 */
class TransferController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/allTransfers/{type}",name="agent_all_transfers")
     */
    public function listTransferAction(Request $request, $type = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if (!$type) {
            $status = array(Transfer::STATUS_PAID, Transfer::STATUS_PAID_RELAY, Transfer::STATUS_PAID_PENDING);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "valide") {
            $status = array(Transfer::STATUS_PAID, Transfer::STATUS_PAID_RELAY, Transfer::STATUS_VALID_B2B, Transfer::STATUS_PAID_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "annule") {
            $status = array(Transfer::STATUS_CANCEL, Transfer::STATUS_PAID_CANCELED);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "paye") {
            $status = array(Transfer::STATUS_PAID);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "immediat") {
            $immediatType = array(Transfer::PRIVATE_TRANSFER_TO_TOWN, Transfer::PRIVATE_TRANSFER_TO_AIRPORT, Transfer::GARE_TRANSFER, Transfer::PARIS_TRANSFER);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('type' => $immediatType, 'status' => Transfer::STATUS_PAID),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "differe") {
            $differeType = array(Transfer::INTERVILLE_TRANSFERT_To_TOWN, Transfer::PORTEAPORTE_TRANSFERT_To_TOWN);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('type' => $differeType, 'status' => Transfer::STATUS_PAID_PENDING),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "recouve") {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => Transfer::STATUS_RECOUVE),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "relais") {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => Transfer::STATUS_PAID_RELAY),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "all") {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findAll();
        }

        return $this->render("@App/Agent/Transfers/listeTransferts.html.twig", array(
            'transfers' => $transfers,
            'b2b' => false,
            'parent' => $parent
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/allTransfersArchives/{type}",name="agent_all_transfers_archives")
     */
    public function listTransferArchivAction(Request $request, $type = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if (!$type) {
            $status = array(Transfer::STATUS_PAID, Transfer::STATUS_CANCEL, Transfer::STATUS_PAID_PENDING);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "paye") {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => Transfer::STATUS_PAID),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "immediat") {
            $immediatType = array(Transfer::PRIVATE_TRANSFER_TO_TOWN, Transfer::PRIVATE_TRANSFER_TO_AIRPORT, Transfer::GARE_TRANSFER, Transfer::PARIS_TRANSFER);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('type' => $immediatType, 'status' => Transfer::STATUS_PAID),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "annule") {
            $status = array(Transfer::STATUS_CANCEL, Transfer::STATUS_PAID_CANCELED);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "differe") {
            $differeType = array(Transfer::INTERVILLE_TRANSFERT_To_TOWN, Transfer::PORTEAPORTE_TRANSFERT_To_TOWN);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('type' => $differeType, 'status' => Transfer::STATUS_PAID_PENDING),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "recouve") {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => Transfer::STATUS_RECOUVE),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "relais") {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => Transfer::STATUS_PAID_RELAY),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "all") {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findAll();
        }

        return $this->render("@App/Agent/Transfers/listeTransfertsArchives.html.twig", array(
            'transfers' => $transfers,
            'b2b' => false,
            'parent' => $parent
        ));
    }


    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/details/{transfer}",name="agent_details_transfer")
     */
    public function detailsTransferAction(Transfer $transfer)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render("@App/Agent/Transfers/transfer_details.html.twig", array(
            'transfer' => $transfer,
            'parent' => $parent
        ));
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modifier/{transfer}", name="agent_modif_transfer")
     */
    public function modifTransferAction(Request $request, Transfer $transfer)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $form = $this->createForm(ModifType::class, $transfer);

        if (!$form->get('roundTrip')->getData()) {
            $form->remove('pickupDate2');
            $form->remove('flight2');
        }


        $adminUserForm = $form->get('passenger');

        $adminUserForm->remove('zipCode');
        $adminUserForm->remove('country');

        $form->remove('roundTrip');


        if ($request->getMethod() == 'GET') {
            return $this->render('@App/Agent/Transfers/modifie.html.twig', array('form' => $form->createView(), 'transfer' => $transfer, 'b2b' => false, 'parent' => $parent));
        } else {
            $oldState = $transfer->getStatus();
            $oldPickupDate = $transfer->getPickupDate();
            $oldPickupDate2 = $transfer->getPickupDate2();

            $form->handleRequest($request);
            if ($oldState == Transfer::STATUS_RECOUVE) {
                if ($form->get('payment')->get('type')->getData() == Transfer::TYPE_CREDIT_CARD) {
                    $transfer->getPayment()->setCreditCardReference($request->get('cb-ref'));
                }
            }
            $newState = $transfer->getStatus();
            $newPickupDate = $transfer->getPickupDate();
            $newPickupDate2 = $transfer->getPickupDate2();

            if ($oldState != $newState) {
                switch ($newState) {
                    case Transfer::STATUS_PAID :
                        $transfer->setStatus(Transfer::STATUS_PAID);
                        $sended = $this->get('transfer.common.service')->sendVoucher($transfer);
                        $this->log(" Mode prod : Paiement :sending voucher and facture immediat");
                        if (!$sended) {
                            $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                        }
                        $sended = $this->get('transfer.common.service')->sendInvoice($transfer);
                        if (!$sended) {
                            $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                        }
                        break;

                    case Transfer::STATUS_CANCEL :
                        //   return $this->render("@App/mails/cancel_mail.html.twig", array('transfert'=>$transfer));
                        $user = $transfer->getCreatedBy();
                        if (in_array('ROLE_RELAY_CUSTOMER', $user->getRoles())) {
                            if ($oldState == Transfer::STATUS_PAID_RELAY) {
                                $details = $user->getRelayCustomerDetail();
                                if ($details != null) {
                                    $new_bonus = $transfer->getPrice();
                                    $bonus = $details->getBonus() + $new_bonus;
                                    $details->setBonus($bonus);
                                    $this->getDoctrine()->getManager()->persist($details);
                                }
                            } else {
                                $details = $user->getRelayCustomerDetail();
                                if ($details != null) {
                                    $new_bonus = $transfer->getPrice() * 0.1;
                                    $bonus = $details->getBonus() - $new_bonus;
                                    $details->setBonus($bonus);
                                    $this->getDoctrine()->getManager()->persist($details);
                                }
                            }
                        }

                        $sended = $this->get('transfer.common.service')->sendCancelEmail($transfer);
                        if (!$sended) {
                            $this->log("SENDING INVOICE TRANSFERT CANCEL Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING INVOICE TRANSFERT CANCEL ECHEC Ref Transfer " . $transfer->getReference());
                        }
                        break;
                }
            }

            if ($oldPickupDate != $newPickupDate || $oldPickupDate2 != $newPickupDate2) {
                $sended = $this->get('transfer.common.service')->sendUpdateDateEmail($transfer);
                if (!$sended) {
                    $this->log("SENDING INVOICE TRANSFERT UPDATE Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("SENDING INVOICE TRANSFERT UPDATE Ref Transfer " . $transfer->getReference());
                }

//                $sended = $this->get('transfer.common.service')->sendVoucher($transfer);
//                $this->log(" Mode prod : Paiement :sending voucher and facture immediat");
//                if (!$sended) {
//                    $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
//                    $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
//                }
                $sended = $this->get('transfer.common.service')->sendInvoice($transfer);
                if (!$sended) {
                    $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                }
            }
            /*
            $date1 = $form->get('pickupdate')->getData()->format('Y-m-d ') . $form->get('time')->getData();

            $transfer->setPickupDate(date_create_from_format('Y-m-d H:i', $date1));

            $flight1 = $this->getDoctrine()->getManager()->merge($transfer->getFlight());
            $transfer->setFlight($flight1);*/

            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', "Modifcation de la commande réussie");

            return $this->render("@App/Agent/Transfers/transfer_details.html.twig", array(
                'transfer' => $transfer,
                'parent' => $parent
            ));

        }
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modifier_b2b/{transfer}", name="agent_modif_transfer_b2b")
     */
    public function modifTransferB2BAction(Request $request, Transfer $transfer)
    {

        $parent = $this->get('common.back.service')->getMenuExtends();
        $form = $this->createForm(ModifType::class, $transfer);

        if (!$form->get('roundTrip')->getData()) {
            $form->remove('pickupDate2');
            $form->remove('flight2');
        }

        $form->remove('status');
        $form->add('status', ChoiceType::class, array(
            'choices' => array(
                'open_b2b' => 'Ouvert',
                'paid_b2b' => 'Payé',
                'cancel_b2b' => 'Annulé',
                'valid_b2b' => 'Validé',
                'wait_b2b' => 'En attente de paiment')));

        $adminUserForm = $form->get('passenger');

        $adminUserForm->remove('zipCode');
        $adminUserForm->remove('country');

        $form->remove('roundTrip');


        if ($request->getMethod() == 'GET') {
            return $this->render('@App/Agent/Transfers/modifie.html.twig', array('form' => $form->createView(), 'transfer' => $transfer, 'b2b' => true, 'parent' => $parent));
        } else {
            $oldState = $transfer->getStatus();
            $oldPickupDate = $transfer->getPickupDate();
            $oldPickupDate2 = $transfer->getPickupDate2();

            $form->handleRequest($request);

            $newState = $transfer->getStatus();
            $newPickupDate = $transfer->getPickupDate();
            $newPickupDate2 = $transfer->getPickupDate2();

            if ($oldState != $newState) {
                switch ($newState) {
                    case Transfer::STATUS_PAID_B2B :
                        $transfer->setStatus(Transfer::STATUS_PAID_B2B);
                        $sended = $this->get('transfer.common.service')->sendVoucherAgency($transfer);
                        $this->log(" Mode prod : Paiement :sending voucher and facture b2b");
                        if (!$sended) {
                            $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                        }
                        $sended = $this->get('transfer.common.service')->sendInvoiceAgency($transfer);
                        if (!$sended) {
                            $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                        }
                        break;

                    case Transfer::STATUS_CANCEL_B2B :
                        $sended = $this->get('transfer.common.service')->sendCancelB2BEmail($transfer);
                        if (!$sended) {
                            $this->log("SENDING INVOICE TRANSFERT CANCEL Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING INVOICE TRANSFERT CANCEL ECHEC Ref Transfer " . $transfer->getReference());
                        }
                        break;

                    case Transfer::STATUS_VALID_B2B :
                        $this->log(" Mode prod : Paiement :sending email valid b2b");

                        $sended = $this->get('transfer.common.service')->sendVoucherAgency($transfer);
                        $this->log(" Mode prod : Paiement :sending voucher b2b");
                        if (!$sended) {
                            $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                        }
                        //send facture
                        $sended = $this->get('transfer.common.service')->sendInvoiceAgency($transfer);
                        if (!$sended) {
                            $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                        }
//                        return $this->render("@App/mails/mail_valid_b2b.html.twig", array('transfer'=>$transfer));
//                        if($transfer->getAffectedTo() != $transfer->getCreatedBy()){
                        $sended = $this->get('transfer.common.service')->sendEmailValidB2B($transfer);
                        if (!$sended) {
                            $this->log("SENDING MAil valid B2B to commercial Ref Transfer " . $transfer->getReference());
                            $this->get('logger')->addCritical("MAil valid B2B to commercial Ref Transfer " . $transfer->getReference());
                        }
//                        }
                        break;


                }
            }

            if ($oldPickupDate != $newPickupDate || $oldPickupDate2 != $newPickupDate2) {
                $sended = $this->get('transfer.common.service')->sendUpdateDateEmail($transfer);
                if (!$sended) {
                    $this->log("SENDING INVOICE TRANSFERT UPDATE Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("SENDING INVOICE TRANSFERT UPDATE Ref Transfer " . $transfer->getReference());
                }

                $sended = $this->get('transfer.common.service')->sendInvoiceAgency($transfer);
                if (!$sended) {
                    $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                }
            }

            $this->getDoctrine()->getManager()->flush();

//var_dump($transfer);die;
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($transfer);
//            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Modifcation de la commande réussie");

            return $this->render("@App/Agent/Transfers/transfer_details.html.twig", array(
                'transfer' => $transfer,
                'parent' => $parent
            ));

        }
    }


    /**
     * @param Transfer $transfer
     * @return JsonResponse
     * @Route("/cancel_transfer/{transfer}",name="agent_cancel_transfer",options={"expose"=true})
     */
    public function cancelTransfer(Request $request, Transfer $transfer)
    {

        $parent = $this->get('common.back.service')->getMenuExtends();
        $form = $this->createFormBuilder()->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) &&
                !in_array($transfer->getStatus(), [Transfer::STATUS_CANCEL, Transfer::STATUS_PAID])
            ) {
                $transfer->setStatus(Transfer::STATUS_CANCEL);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', "Commande annulée avec succès");

                return $this->redirectToRoute("admin_all_transfers");
            } else if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                $url = $this->get('router')->generate('homepage');
                Return $this->redirect($url);
            } else {
                return $this->redirectToRoute("admin_all_transfers");
            }
        } else {
            return new JsonResponse(array(
                'html' => $this->renderView("@App/Agent/Transfers/cancel_form.html.twig", array(
                    'form' => $form->createView(),
                    'transfer' => $transfer,
                    'parent' => $parent
                ))
            ));
        }
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/persondetails/{id}", name="agent_transfer_person_details")
     */
    public function transferPersonDetails(Transfer $transfer)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render("@App/Agent/Transfers/transfer_person_details.html.twig", array('transfer' => $transfer, 'parent' => $parent));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/my_transfer/{type}",name="ag_my_transfers")
     */
    public function myTransferListAll($type = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $status = array(Transfer::STATUS_PAID, Transfer::STATUS_PAID_PENDING, Transfer::STATUS_CANCEL);
        $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
            array('createdBy' => $this->getUser(), 'status' => $status),
            array('createdAt' => 'desc'),
            null,
            null);
        if (!$type) {
            return $this->render("@App/Agent/Transfers/listeTransferts.html.twig", array(
                'transfers' => $transfers,
                'b2b' => false,
                'parent' => $parent
            ));
        } else {
            return $this->render("@App/Agent/Transfers/listeTransfertsArchives.html.twig", array(
                'transfers' => $transfers,
                'b2b' => false,
                'parent' => $parent
            ));
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/my_transfers/{agent}",name="agent_my_transfers")
     */
    public function myTransfersListAll(User $agent = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if (!$agent) {
            $agent = $this->getUser();
        }
        $status = array(Transfer::STATUS_PAID, Transfer::STATUS_PAID_PENDING, Transfer::STATUS_VALID_B2B, Transfer::STATUS_PAID_B2B);
        $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
            array('createdBy' => $agent, 'status' => $status),
            array('createdAt' => 'desc'),
            null,
            null);

        return $this->render("@App/Agent/Transfers/listeTransfertsAll.html.twig", array(
            'transfers' => $transfers,
            'parent' => $parent
        ));
    }


    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->container->getParameter('kernel.logs_dir') . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/tous-transferts", name="agent_all_transactions")
     */
    public function getAllTransferAction(Request $request)
    {
        if (!in_array('ROLE_SECRETARY', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('homepage');
        }
        $parent = $this->get('common.back.service')->getMenuExtends();
        $transfers = null;
        $start_date = $request->get('start_date') ? new \DateTime($request->get('start_date')) : new \DateTime();
        $end_date = $request->get('end_date') ? new \DateTime($request->get('end_date')) : new \DateTime();

        if ($request->getMethod() == "POST") {
            $em = $this->getDoctrine()->getManager();

            $transfers = $em->getRepository('AppBundle:Transfer')->getAllTransferPerPeriod(
                $start_date->format('Y-m-d H:i:s'),
                $end_date->setTime(23, 59, 59)->format('Y-m-d H:i:s')
            );
        }

        return $this->render('@App/Agent/Transfers/allTransfers.html.twig', array(
            'transfers' => $transfers,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'parent' => $parent
        ));
    }

}