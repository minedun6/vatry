<?php

namespace AppBundle\Controller\PartnerAgency;


use AppBundle\Entity\Transfer;
use AppBundle\Entity\Payment;
use AppBundle\Entity\B2BInvoice;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentController
 * @package AppBundle\Controller\PartnerAgency
 * @Route("/part_agency")
 */
class PaymentController extends Controller
{
    /**
     * @param Transfer $transfer
     * @param Request $request
     * @return Response
     * @Route("/payment/{transfer}", name="payement_b2b_partner")
     */
    public function payPartnerAgencyAction(Request $request, Transfer $transfer)
    {

        if (!$transfer) {
            return $this->redirectToRoute("homepage");
        } else {
            $securityContext = $this->container->get('security.authorization_checker');
            $em = $this->getDoctrine()->getManager();

            $payment = new Payment();
            $cbRef = null;

            $transfer->setAffectedTo($this->getUser());

//            $payment->setType(Transfer::TYPE_B2B);

            $transfer->setStatus(Transfer::STATUS_VALID_B2B);

            $b2bInvoice = new B2BInvoice();
            $date = new \DateTime("now");
            $date->format('y-MM-dd');
            //    $date->modify('-1 month');

            $year = $date->format('Y');
            $month = $date->format('m');
            $totalPersons = $transfer->getQty() + $transfer->getQtyChild() + $transfer->getQtyBaby();

            $partnerAgency = $transfer->getAffectedTo()->getAgencePartner();
            $b2bInvoice->setCommission($partnerAgency->getCommission());
            $b2bInvoice->setTransfer($transfer);
            $b2bInvoice->setPartnerAgency($partnerAgency);
            $b2bInvoice->setPrice($transfer->getPrice());
            if ($transfer->getType() == Transfer::INTERVILLE_TRANSFERT_To_TOWN || $transfer->getType() == Transfer::PORTEAPORTE_TRANSFERT_To_TOWN || $transfer->getType() == Transfer::GARE_TRANSFER) {
                $b2bInvoice->setNetPrice($transfer->getPrice());
            } else {
                $prixHT = $transfer->getPrice() * 100 / 110;
                $commission = ($prixHT * $partnerAgency->getCommission()) / 100;
                $b2bInvoice->setNetPrice($transfer->getPrice() - $commission);
            }
            $b2bInvoice->setYear($year);
            $b2bInvoice->setMonth($month);
            $b2bInvoice->setTotalPerson($totalPersons);

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

            /**********************/
            $payment->setPerson($transfer->getCreatedBy()->getPerson());
            $payment->setAmount($transfer->getPrice());
            $transfer->setPayment($payment);

            $em->persist($transfer);
            $em->persist($payment);
            $em->persist($b2bInvoice);

            $em->flush();

            $this->addFlash('success', 'Opération de paiement avec succès');

            // J'ai changé le type à valid et le affected à false
            return $this->redirectToRoute('agency_all_transfers', array(
                //    'type' => "wait",
                'type' => "valid",
                //    'affected' => true
                //    'affected' => false
            ));
        }
    }

    /**
     * @return Response
     * @Route("/confirm_b2b", name="confirm_b2b")
     */
    public function confirm_b2bAction()
    {
        return $this->redirectToRoute('agency_all_transfers', array(
            'type' => "wait",
            'affected' => true
        ));
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/validate_b2b", name="agency_validate_b2b")
     */
    public function validate_b2bAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $idTransfert = $request->request->get('transfert');
            $transfer = $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->find($idTransfert);

            if (!$transfer) {
                $this->addFlash('error', "Commande last minute inexistante !");
            } else {
                $em = $this->getDoctrine()->getManager();

                $transfer->setStatus(Transfer::STATUS_VALID_B2B);

                $b2bInvoice = new B2BInvoice();

                $date = new \DateTime("now");
                $date->format('y-MM-dd');
                //      $date->modify('-1 month');

                $year = $date->format('Y');
                $month = $date->format('m');
                $totalPersons = $transfer->getQty() + $transfer->getQtyChild() + $transfer->getQtyBaby();

                $partnerAgency = $transfer->getAffectedTo()->getAgencePartner();
                $b2bInvoice->setCommission($partnerAgency->getCommission());
                $b2bInvoice->setTransfer($transfer);
                $b2bInvoice->setPartnerAgency($partnerAgency);
                $b2bInvoice->setPrice($transfer->getPrice());
                $b2bInvoice->setNetPrice($transfer->getPrice() * (1 - $partnerAgency->getCommission() / 100));
                $b2bInvoice->setYear($year);
                $b2bInvoice->setMonth($month);
                $b2bInvoice->setTotalPerson($totalPersons);
                $em->persist($transfer);
                $em->persist($b2bInvoice);
                $em->flush();

                $this->log(" Mode prod : Paiement :sending email valid b2b");

                $sended = $this->get('transfer.common.service')->sendVoucherAgency($transfer);
                $this->log(" Mode prod : Paiement :sending voucher b2b");
                if (!$sended) {
                    $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                }
                //send facture
                //$sended = $this->get('transfer.common.service')->sendInvoiceAgency($transfer);
                /*if (!$sended) {
                    $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                }*/

                if ($transfer->getAffectedTo() != $transfer->getCreatedBy()) {
                    $sended = $this->get('transfer.common.service')->sendEmailValidB2B($transfer);
//                    return $this->render("@App/mails/mail_valid_b2b.html.twig", array('transfert'=>$transfer));
                    if (!$sended) {
                        $this->log("SENDING MAil valid B2B to commercial Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("MAil valid B2B to commercial Ref Transfer " . $transfer->getReference());
                    }
                }

                $this->addFlash('success', 'Commande validée avec succès');
            }
        }

        return $this->redirectToRoute('agency_all_transfers', array(
            'type' => "valid",
            'affected' => true
        ));
    }

    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->container->getParameter('kernel.logs_dir') . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }

}