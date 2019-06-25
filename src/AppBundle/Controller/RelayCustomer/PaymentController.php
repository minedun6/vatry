<?php
/**
 * Created by PhpStorm.
 * User: khoubeib
 * Date: 7/26/2016
 * Time: 5:07 PM
 */

namespace AppBundle\Controller\RelayCustomer;


use AppBundle\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Payment;

/**
 * Class PaymentController
 * @package AppBundle\Controller\RelayCustomer
 * @Route("/client-relais")
 */
class PaymentController extends Controller
{
    /**
     * @param Transfer $transfer
     * @param Request $request
     * @return Response
     * @Route("/paye/{transfer}", name="relay_customer_pay")
     */
    public function payAction(Request $request, Transfer $transfer)
    {
        $referer = $referer = $request->headers->get('referer');
        $em = $this->getDoctrine()->getManager();
        if (!$transfer) {
            return new RedirectResponse($referer);
        } else {
            $securityContext = $this->container->get('security.authorization_checker');
            if ($securityContext->isGranted('ROLE_RELAY_CUSTOMER')) {
                $user = $this->getUser();
                $details = $user->getRelayCustomerDetail();

                if ($details != null) {
                    $bonus = $details->getBonus();
                    if ($bonus >= $transfer->getPrice()) {
                        $details->setBonus($bonus - $transfer->getPrice());
                        $em->persist($details);

                        $payment = new Payment();
                        $payment->setType(Transfer::TYPE_RELAY_PAY);
                        $transfer->setStatus(Transfer::STATUS_PAID_RELAY);

                        $sended = $this->get('transfer.common.service')->sendVoucher($transfer);
                        if (!$sended) {
                            $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                        }

                        $sended = $this->get('transfer.common.service')->sendInvoice($transfer);
                        if (!$sended) {
                            $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                        }

                        $payment->setPerson($transfer->getCreatedBy()->getPerson());
                        $payment->setAmount($transfer->getPrice());
                        $transfer->setPayment($payment);

                        $em->persist($payment);
                        $em->persist($transfer);

                        $em->flush();
                    }
                }
            } else {
                return new RedirectResponse($referer);
            }

            $this->addFlash('success', 'Opération de paiement avec votre Bonus rélisée avec succès');

            return $this->redirectToRoute('relay_customer_transfers');
        }
    }


    /**
     * @param Transfer $transfer
     * @Route("/relais_test_payment/{id}", name="test_payment")
     */
    public function pay(Transfer $transfer)
    {
        $transfer->setStatus(Transfer::STATUS_PAID);
        $sended = $this->get('transfer.common.service')->sendVoucher($transfer);

        $sended = $this->get('transfer.common.service')->sendInvoice($transfer);


        //test on user type if relay customer add bonus
        if (in_array('ROLE_RELAY_CUSTOMER', $this->getUser()->getRoles())) {
            $details = $this->getUser()->getRelayCustomerDetail();
            if ($details) {
                $new_bonus = $transfer->getPrice() * 0.1;
                $bonus = $details->getBonus() + $new_bonus;
                $details->setBonus($bonus);
                $this->getDoctrine()->getManager()->persist($details);
            }
        }
        $this->getDoctrine()->getManager()->flush($details);
        $this->getDoctrine()->getManager()->flush($transfer);
        return $this->redirectToRoute('homepage');
    }

    /**
     * @return Response
     * @Route("/confirm_rc", name="confirm_rc")
     */
    public function confirm_rcAction()
    {    //to check
        return $this->redirectToRoute('rc_all_transfers', array(
            'type' => "wait",
            'affected' => true
        ));
    }



    /**
     * @param Request $request
     * @return Response
     * @Route("/validate_rc", name="validate_rc")
     */
    public function validate_rcAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $idTransfert = $request->request->get('transfert');
            $transfer = $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->find($idTransfert);

            if (!$transfer) {
                $this->addFlash('error', "Commande last minute inexistante !");
            } else {
                $em = $this->getDoctrine()->getManager();

                $transfer->setStatus(Transfer::STATUS_VALID_RC);

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


                if ($transfer->getAffectedTo() != $transfer->getCreatedBy()) {
                    $sended = $this->get('transfer.common.service')->sendEmailValidB2B($transfer);


                    if (!$sended) {
                        $this->log("SENDING MAil valid RC to commercial Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("MAil valid B2B to commercial Ref Transfer " . $transfer->getReference());
                    }
                }

                $this->addFlash('success', 'Commande validée avec succès');
            }
        }

        return $this->redirectToRoute('relay_customer_transfers');
    }


}
