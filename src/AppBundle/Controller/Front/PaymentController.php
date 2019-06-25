<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 05/06/2016
 * Time: 21:47
 */

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Transfer;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentController
 * @package AppBundle\Controller\Front
 * @Route("/payment")
 */
class PaymentController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/bank_return_interface",name="bank_return_interface")
     */
    public function renderPaymentFormAction(Request $request)
    {
        $this->log('BEGIN PAYMENT');
        $this->log('path : ' . $this->container->getParameter('kernel.logs_dir'));
        //POST DATA
        $postData = $request->request->getIterator();
        try {
            $ok = $this->get('payment.service')->verifyDataFomBank($postData);
            $this->log("PAYMENT INTERFACE " . json_encode($postData));

            if ($ok) {
                $this->log('Result OK ');
                $this->__processPayment($postData);
            } else {
                $this->log('Result NOK ');
            }
        } catch (\Exception $e) {
            $this->log('EXCEPTION ' . $e->getMessage());
            $ok = false;
            $this->get('logger')->addCritical('PAYMENT EXCEPTION => ' . $e->getMessage());
        }

        $body = "version=2" . "\r\ncdr=";
        if ($ok) {
            $body = $body . "0";
        } else {
            $body = $body . "1";
        }
        $response = new Response($body);
        $response->headers->set('Content-type', 'text/plain');

        return $response;
    }

    /**
     * @param Transfer $transfer
     * @return Response
     * @Route("/test_send/{transfer}")
     */
    public function sendVoucherAndFacture(Transfer $transfer)
    {

        $sended = $this->get('transfer.common.service')->sendVoucher($transfer);
        var_dump($sended);

        $sended = $this->get('transfer.common.service')->sendInvoice($transfer);
        var_dump($sended);

        $sended = $this->get('transfer.common.service')->sendConfirmationForLaterPayment($transfer);
        var_dump($sended);

        return new Response("");
    }

    private function __processPayment($bankData)
    {
        $this->log('BEGIn PROCESSING ');
        $codeRetour = Helpers::issetOrNull($bankData, 'code-retour');

        //Getting the transfer
        $numTransfer = Helpers::issetOrNull($bankData, 'reference');
        $transfer = $this->getDoctrine()->getRepository("AppBundle:Transfer")
            ->findOneBy(array(
                'referenceWithBank' => $numTransfer
            ));

        if (!$transfer) {
            throw new \Exception("Transfer not found referenceWithBank " . $numTransfer);
        }
        $this->log("UPDATE TRANSFER " . $transfer->getId());
        switch (strtolower($codeRetour)) {
            case 'payetest' :
                $this->log("PAID TEST " . $transfer->getReference());
                if ($this->get('payment.service')->paymentType($transfer) == 'immediat') {
                    $transfer->setStatus(Transfer::STATUS_PAID_TEST);
                    $sended = $this->get('transfer.common.service')->sendVoucher($transfer);
                    $this->log("sending voucher and facture immediat");
                    if (!$sended) {
                        $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                    }
                    $sended = $this->get('transfer.common.service')->sendInvoice($transfer);
                    if (!$sended) {
                        $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                    }
                } else {
                    $transfer->setStatus(Transfer::STATUS_PAID_PENDING);
                    $sended = $this->get('transfer.common.service')->sendConfirmationForLaterPayment($transfer);
                    $this->log("sending confirmation");
                    if (!$sended) {
                        $this->log("SENDING CONFIRMATION PENDING MSG ECHEC Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("SENDING CONFIRMATION PENDING MSG ECHEC Ref Transfer " . $transfer->getReference());
                    }
                }
                break;
            case 'paiement' :
                $this->log("PAID  " . $transfer->getReference());
                if ($this->get('payment.service')->paymentType($transfer) == 'immediat') {
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

                    //test on user type if relay customer add bonus
                    if (in_array('ROLE_RELAY_CUSTOMER', $transfer->getCreatedBy()->getRoles())) {
                        $details = $this->getUser()->getRelayCustomerDetail();
                        if ($details) {
                            $new_bonus = $transfer->getPrice() * 0.1;
                            $bonus = $details->getBonus() + $new_bonus;
                            $details->setBonus($bonus);
                            $this->getDoctrine()->getManager()->persist($details);
                        }
                    }
                } else {
                    $transfer->setStatus(Transfer::STATUS_PAID_PENDING);
                    $sended = $this->get('transfer.common.service')->sendConfirmationForLaterPayment($transfer);
                    $this->log(" Mod Production : Paiement différé : sending confirmation");
                    if (!$sended) {
                        $this->log("SENDING CONFIRMATION PENDING MSG ECHEC Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("SENDING CONFIRMATION PENDING MSG ECHEC Ref Transfer " . $transfer->getReference());
                    }
                }
                break;
            case 'annulation' :
                $this->log("PAID CANCEL " . $transfer->getReference());
                $transfer->setStatus(Transfer::STATUS_PAID_CANCELED);
                break;
        }
        $this->log("FLUSH " . $transfer->getId());
        $this->getDoctrine()->getManager()->flush();
    }


    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->container->getParameter('kernel.logs_dir') . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }

}