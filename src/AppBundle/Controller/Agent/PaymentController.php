<?php

namespace AppBundle\Controller\Agent;


use AppBundle\Entity\Transfer;
use AppBundle\Entity\Payment;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PaymentController
 * @package AppBundle\Controller\Agent
 * @Route("/agent")
 */
class PaymentController extends Controller
{
    /**
     * @param Transfer $transfer
     * @param String $type
     * @param Request $request
     * @return Response
     * @Route("/payment/{transfer}/{type}", name="payement_agent")
     */
    public function payAgentImmediatAction(Request $request, Transfer $transfer, $type = null)
    {
        if (!$transfer) {
            return $this->redirectToRoute("agent_all_transfers", array('type' => "all"));
        } else {
            $securityContext = $this->container->get('security.authorization_checker');
            $em = $this->getDoctrine()->getManager();

            $payment = new Payment();
            $cbRef = null;
            if ($type == 'cb') {
                $cbRef = $request->request->get('cbRef');
                $payment->setCreditCardReference($cbRef);
                $payment->setType(Transfer::TYPE_CREDIT_CARD);
            } elseif ($type == 'cach')
                $payment->setType(Transfer::TYPE_CACHE);
            elseif ($type == 'b2b') {
                $id = $request->request->get('affectedTo');
                $affectedTo = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);
                $transfer->setAffectedTo($affectedTo);
                $this->recalculatePrice($transfer);
//                $payment->setType(Transfer::TYPE_B2B);
            } elseif ($type == 'rc') {
                $id = $request->request->get('affectedTo');
                $affectedTo = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);
                $transfer->setAffectedTo($affectedTo);
//                $payment->setType(Transfer::TYPE_B2B);
            } elseif ($type == 'recouve') {
                $payment->setType(null);
            } else {
                $this->addFlash('error', "Echec lors du paiement");

                if ($securityContext->isGranted('ROLE_ADMIN'))
                    return $this->redirectToRoute("admin_all_transfers", array('type' => "all"));
                elseif ($securityContext->isGranted('ROLE_SECRETARY') || $securityContext->isGranted('ROLE_COMMERCIAL') || $securityContext->isGranted('ROLE_AGENT'))
                    return $this->redirectToRoute("agent_all_transfers", array('type' => "all"));
            }

            /**********************/

            if (($type != 'b2b') && ($type != 'rc'))  {
                if ($type == 'recouve') {
                    $transfer->setStatus(Transfer::STATUS_RECOUVE);
                } else {
                    $transfer->setStatus(Transfer::STATUS_PAID);
                }
            } elseif ($type == 'b2b')
                { $transfer->setStatus(Transfer::STATUS_WAIT_B2B);
                }
              else {
              $transfer->setStatus(Transfer::STATUS_WAIT_RC );
              }


//            var_dump($id);
//            var_dump($transfer->getAffectedTo()->getId());
//            var_dump($transfer->getCreatedBy()->getId());
//            var_dump($transfer->getStatus());
//            die;

            if ( ($type != 'b2b') && ($type != 'rc') ) {
                $sended = $this->get('transfer.common.service')->sendVoucher($transfer);
                $this->log(" Mode prod : Paiement :sending voucher and facture immediat");
                if (!$sended) {
                    $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                }
                if ($type != 'recouve') {
                    $sended = $this->get('transfer.common.service')->sendInvoice($transfer);
                    if (!$sended) {
                        $this->log("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("SENDING INVOICE ECHEC Ref Transfer " . $transfer->getReference());
                    }
                }
            }

            elseif ($type=='rc') {
                  $this->log(" Mode prod : Paiement :sending voucher rc");
                $sended = $this->get('transfer.common.service')->sendEmailConfirmRC($transfer);
//                return $this->render("@App/mails/mail_confirm_b2b.html.twig", array('transfer'=>$transfer));
                if (!$sended) {
                    $this->log("SENDING MAil confirm RC to relay customer Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("MAil confirm RC to Relay client Ref Transfer " . $transfer->getReference());
                }
            }
             else {
                $this->log(" Mode prod : Paiement :sending voucher b2b");
                $sended = $this->get('transfer.common.service')->sendEmailConfirmB2B($transfer);
//                return $this->render("@App/mails/mail_confirm_b2b.html.twig", array('transfer'=>$transfer));
                if (!$sended) {
                    $this->log("SENDING MAil confirm B2B to partner agency Ref Transfer " . $transfer->getReference());
                    $this->get('logger')->addCritical("MAil confirm B2B to partner agency Ref Transfer " . $transfer->getReference());
                }
            }

            //$payment->setPerson($transfer->getPassenger());
            $payment->setPerson($transfer->getCreatedBy()->getPerson());
            $payment->setAmount($transfer->getPrice());
//            $transfer->setStatus(Transfer::STATUS_PAID);
            $transfer->setPayment($payment);

            $em->persist($payment);
            $em->persist($transfer);

            $em->flush();

            $this->addFlash('success', 'Opération de paiement avec succès');

            if ($securityContext->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute("admin_all_transfers");
            } elseif ($securityContext->isGranted('ROLE_SECRETARY') || $securityContext->isGranted('ROLE_COMMERCIAL') || $securityContext->isGranted('ROLE_AGENT')) {
                if ($type == 'b2b')
                    return $this->redirectToRoute("com_agency_all_transfers");

                return $this->redirectToRoute("agent_all_transfers");
            } elseif ($securityContext->isGranted('ROLE_AGENT'))
                return $this->redirectToRoute("agent_all_transfers");
        }
    }

    public function checkAgencyByEmail($email, $id = null)
    {
        $agency = $this->getDoctrine()->getRepository("AppBundle:Agence")->findOneByEmail($email);

        if ($id)
            $agencyToUpdate = $this->getDoctrine()->getRepository("AppBundle:Agence")->findOneById($id);

        if ($agency) {
            if ($id) {
                if ($agency == $agencyToUpdate) return false;
                return true;
            } else return true;
        } else return false;
    }

    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->container->getParameter('kernel.logs_dir') . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }

    private function recalculatePrice(Transfer $transfer)
    {
        switch ($transfer->getType()) {
            case Transfer::GARE_TRANSFER:
                $price = $this->get('gare.transfer.service')->calculateTarif($transfer, "partneragency");
                $prix = $price['prix'];
                $transfer->setPrice($prix);
                break;
            case Transfer::INTERVILLE_TRANSFERT_To_TOWN:
                $price = $this->get('transfer.interville.service')->calculateTarif($transfer, "partneragency");
                $prix = $price['prix'];
                $transfer->setPrice($prix);
                break;
            case Transfer::PORTEAPORTE_TRANSFERT_To_TOWN:
                $price = $this->get('transfer.porteaporte.service')->calculateTarif($transfer, "partneragency");
                $prix = $price['prix'];
                $transfer->setPrice($prix);
                break;
            case Transfer::PRIVATE_TRANSFER_TO_AIRPORT:
                $price = $this->get('transfer.privateaeroport.service')->calculateTarif($transfer, true);
                $prixHT = $transfer->getPrice() * 100 / 110;
                $com = ($prixHT * $transfer->getAffectedTo()->getAgencePartner()->getCommission()) / 100;
                $transfer->setCommission($com);
                $price = $this->get('transfer.privateaeroport.service')->calculateTarif($transfer,true);
                $transfer->setPrice($price['price'] - $com);
                break;
            case Transfer::PRIVATE_TRANSFER_TO_TOWN:
                $price = $this->get('transfer.private.service')->calculateTarif($transfer, true);
                $prixHT = $transfer->getPrice() * 100 / 110;
                $com = ($prixHT * $transfer->getAffectedTo()->getAgencePartner()->getCommission()) / 100;
                $transfer->setCommission($com);
                $price = $this->get('transfer.privateaeroport.service')->calculateTarif($transfer,true);
                $transfer->setPrice($price['price'] - $com);
                break;
        }
    }
    
}
