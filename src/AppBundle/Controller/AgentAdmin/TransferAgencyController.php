<?php

namespace AppBundle\Controller\AgentAdmin;


use AppBundle\Entity\Agence;
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
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/agent-admin/gestion_transfer")
 */
class TransferAgencyController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/allTransfers/{type}",name="com_agency_all_transfers")
     */
    public function listTransferAction(Request $request, $type = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if ($type == "paye") {
            $status = array(Transfer::STATUS_PAID_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "annule") {
            $status = array(Transfer::STATUS_CANCEL_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "valid") {
            $status = array(Transfer::STATUS_VALID_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "wait") {
            $status = array(Transfer::STATUS_WAIT_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "all" or !$type) {
            $status = array(Transfer::STATUS_PAID_B2B, Transfer::STATUS_CANCEL_B2B, Transfer::STATUS_VALID_B2B, Transfer::STATUS_WAIT_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        }

        return $this->render("@App/Agent/Transfers/listeTransferts.html.twig", array(
            'transfers' => $transfers,
            'b2b' => 'b2b',
            'parent' => $parent
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/allTransfersArchives/{type}",name="com_agency_all_transfers_archives")
     */
    public function listTransferArchivAction(Request $request, $type = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if ($type == "paye") {
            $status = array(Transfer::STATUS_PAID_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "annule") {
            $status = array(Transfer::STATUS_CANCEL_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "valid") {
            $status = array(Transfer::STATUS_VALID_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "wait") {
            $status = array(Transfer::STATUS_WAIT_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        } else if ($type == "all") {
            $status = array(Transfer::STATUS_PAID_B2B, Transfer::STATUS_CANCEL_B2B, Transfer::STATUS_VALID_B2B, Transfer::STATUS_WAIT_B2B);
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
                array('status' => $status),
                array('createdAt' => 'desc'),
                null,
                null);
        }

        return $this->render("@App/Agent/Transfers/listeTransfertsArchives.html.twig", array(
            'transfers' => $transfers,
            'b2b' => 'b2b',
            'parent' => $parent
        ));
    }


    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/details/{transfer}",name="com_agency_details_transfer")
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
     * @Route("/modifier/{transfer}", name="com_agency_modif_transfer")
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
            return $this->render('@App/Agent/Transfers/modifie.html.twig', array('form' => $form->createView(), 'transfer' => $transfer));
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
     * @return JsonResponse
     * @Route("/cancel_transfer/{transfer}",name="com_agency_cancel_transfer",options={"expose"=true})
     */
    public function cancelTransfer(Request $request, Transfer $transfer)
    {

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
                    'transfer' => $transfer
                ))
            ));
        }
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/persondetails/{id}", name="com_agency_transfer_person_details")
     */
    public function transferPersonDetails(Transfer $transfer)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render("@App/Agent/Transfers/transfer_person_details.html.twig", array(
            'transfer' => $transfer,
            'parent' => $parent
        ));
    }

    /**
     * @param User $agent
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/agent_transfer/{agent}",name="com_agency_agent_transfers")
     */
    public function agentsTransferList(User $agent = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if (!$agent) {
            $types = array('agent', 'agentAdmin');

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->createQueryBuilder('t')
                ->where('t.createdBy is not null')
                ->join('t.createdBy', 'u', 'WITH', 'u.type IN (:type)')
                ->setParameter('type', $types)
                ->getQuery()->getResult();
        } else {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findBy(array('createdBy' => $agent));
        }


        return $this->render("@App/Agent/Transfers/listeTransferts.html.twig", array(
            'transfers' => $transfers,
            'parent' => $parent
        ));
    }

    /**
     * @param Agence $agency
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/agency_transfer/{agency}",name="list_agency_com_transfers")
     */
    public function agencyTransferList(Agence $agency = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if (!$agency) {
            $types = array('partneragency');

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->createQueryBuilder('t')
                ->where('t.affectedTo is not null')
                ->andWhere('t.affectedTo = :user', 'u', 'WITH', 'u.type IN (:type)')
                ->setParameter('type', $types)
                ->getQuery()->getResult();
        } else {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findBy(array('affectedTo' => $agency->getAgencePartner()->getUser()));
        }


        return $this->render("@App/Agent/Transfers/listeTransfertsAll.html.twig", array(
            'transfers' => $transfers,
            'b2b' => 'b2b',
            'parent' => $parent
        ));
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/my_transfer/{type}",name="com_agency_owner_transfers")
     */
    public function myTransferListAll($type = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
            ->createQueryBuilder('t')
            ->where('t.createdBy is not null')
            ->join('t.createdBy', 'u', 'WITH', 'u = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()->getResult();
        if (!$type) {
            return $this->render("@App/Agent/Transfers/listeTransferts.html.twig", array(
                'transfers' => $transfers
            ));
        } else {
            return $this->render("@App/Agent/Transfers/listeTransfertsArchives.html.twig", array(
                'transfers' => $transfers,
                'parent' => $parent
            ));
        }
    }

    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->container->getParameter('kernel.logs_dir') . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }

}