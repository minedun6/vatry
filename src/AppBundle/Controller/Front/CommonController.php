<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 21/05/2016
 * Time: 09:50
 */

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Transfer;
use AppBundle\Entity\MonthlyInvoice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CommonController extends Controller
{

    /**
     * @param Transfer $transfer
     * @param $yesNo
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/payment_result/{yesNo}",name="payment_result")
     */
    public function payAction($yesNo = null)
    {

        if ($yesNo == null) {
            return $this->render("@App/front/common/payment.html.twig", array(
                'yesNo' => '0'
            ));
        }

        if ($yesNo == '1') {
            //OK

            //SENDING MAIL
            /*            if (!in_array($transfer->getType(), ['interville_airporttown', 'porteaporte_airporttown'])) {
                            $sended = $this->get('transfer.common.service')->sendVoucher($transfer);
                        } else {
                            $sended = $this->get('transfer.common.service')->sendConfirmationMsg($transfer);
                        }

                        if (!$sended) {
                            $this->get('session')->getFlashBag()->add('error', "Problème lors de l'envoi du Voucher");
                        }*/
            return $this->render("@App/front/common/payment.html.twig", array(
                'yesNo' => '1'
            ));
        }

        if ($yesNo == '0') {
            //NOK
            return $this->render("@App/front/common/payment.html.twig", array(
                'yesNo' => '0'
            ));
        }

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/render_voucher/{transfer}",name="render_voucher")
     */
    public function renderVoucher(Transfer $transfer)
    {


        return $this->render("@App/pdf/voucher_v2.html.twig", array(
            'transfer' => $transfer
        ));
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/facture/{transfer}/{type}",name="facture")
     */
    public function generateFacture(Transfer $transfer = null, $type = 'pdf')
    {
        if ($transfer != null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser() == $transfer->getCreatedBy())) {

            if ($type == 'html') {

                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles())) {
                    return $this->render("@App/pdf/factureAgent.html.twig", array('transfer' => $transfer));
                } else {
                    return $this->render("@App/pdf/facture.html.twig", array('transfer' => $transfer));
                }
            } else {
                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles())) {
                    $factureHtml = $this->renderView("@App/pdf/factureAgent.html.twig", array('transfer' => $transfer));
                } else {
                    $factureHtml = $this->renderView("@App/pdf/facture.html.twig", array('transfer' => $transfer));
                }
                $filepath = $this->container->getParameter('tmp_dir') . "/facture" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

                $html2Pdf = $this->get('html2pdf_factory')->create('L');
                $html2Pdf->writeHTML($factureHtml);
                $html2Pdf->Output($filepath, 'F');

                $response = new Response();
                // Set headers
                $response->headers->set('Cache-Control', 'private');
                $response->headers->set('Content-type', mime_content_type($filepath));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filepath) . '";');
                $response->headers->set('Content-length', filesize($filepath));

                // Send headers before outputting anything
                $response->sendHeaders();

                $response->setContent(file_get_contents($filepath));
                return $response;
            }
        } else {
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }

    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/facture_b2b/{transfer}/{type}",name="facture_b2b")
     */
    public function generateFactureB2B(Transfer $transfer = null, $type = 'pdf')
    {
        if ($transfer != null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser() == $transfer->getCreatedBy() || $this->getUser() == $transfer->getAffectedTo())) {

            if ($type == 'html') {

                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles())) {
                    return $this->render("@App/pdf/facturePartnerAgency.html.twig", array('transfer' => $transfer));
                } else {
                    return $this->render("@App/pdf/facture.html.twig", array('transfer' => $transfer));
                }
            } else {
                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles())) {
                    $factureHtml = $this->renderView("@App/pdf/facturePartnerAgency.html.twig", array('transfer' => $transfer));
                } else {
                    $factureHtml = $this->renderView("@App/pdf/facture.html.twig", array('transfer' => $transfer));
                }
                $filepath = $this->container->getParameter('tmp_dir') . "/facture" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

                $html2Pdf = $this->get('html2pdf_factory')->create('L');
                $html2Pdf->writeHTML($factureHtml);
                $html2Pdf->Output($filepath, 'F');

                $response = new Response();
                // Set headers
                $response->headers->set('Cache-Control', 'private');
                $response->headers->set('Content-type', mime_content_type($filepath));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filepath) . '";');
                $response->headers->set('Content-length', filesize($filepath));

                // Send headers before outputting anything
                $response->sendHeaders();

                $response->setContent(file_get_contents($filepath));
                return $response;
            }
        } else {
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }

    }

    /**
     * @param MonthlyInvoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/facture_monthly_b2b/{invoice}/{type}",name="facture_monthly_b2b")
     */
    public function generateMonthlyFactureB2B(MonthlyInvoice $invoice = null, $type = 'pdf')
    {

        if ($invoice != null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser()->getAgencePartner() == $invoice->getPartnerAgency())) {
            if ($type == 'html') {

//                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ||in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) ||in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) ||in_array('ROLE_AGENT', $this->getUser()->getRoles()) ||in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles()))
//                {
                return $this->render("@App/pdf/factureMonthlyB2B.html.twig", array('invoice' => $invoice));
//                }
            } else {
//                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ||in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) ||in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) ||in_array('ROLE_AGENT', $this->getUser()->getRoles()) ||in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles())){
                $factureHtml = $this->renderView("@App/pdf/factureMonthlyB2B.html.twig", array('invoice' => $invoice));
//                }else{
//                    $factureHtml = $this->renderView("@App/pdf/factureMonthlyB2B.html.twig", array('invoice' => $invoice));
//                }

                $namePdf = $invoice->getMonth() . '_' . $invoice->getYear();
                if ($invoice->getMonth() < 10) $namePdf = '0' . $namePdf;

                $namePdf = '_' . $namePdf;

                $filepath = $this->container->getParameter('tmp_dir') . "/facture" . str_replace('-', '_', $namePdf) . ".pdf";

                $html2Pdf = $this->get('html2pdf_factory')->create('L');
                $html2Pdf->writeHTML($factureHtml);
                $html2Pdf->Output($filepath, 'F');

                $response = new Response();
                // Set headers
                $response->headers->set('Cache-Control', 'private');
                $response->headers->set('Content-type', mime_content_type($filepath));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filepath) . '";');
                $response->headers->set('Content-length', filesize($filepath));

                // Send headers before outputting anything
                $response->sendHeaders();

                $response->setContent(file_get_contents($filepath));
                return $response;
            }
        } else {
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }

    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/voucher_b2b/{transfer}/{type}",name="voucher_b2b")
     */
    public function generateVoucherB2B(Transfer $transfer = null, $type = 'pdf')
    {
        if ($transfer != null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser() == $transfer->getCreatedBy() || $this->getUser() == $transfer->getAffectedTo())) {
            // !!!!!!
            if ($type == 'html') {
                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles())) {
                    return $this->render("@App/pdf/voucherPartnerAgency.html.twig", array('transfer' => $transfer));
                } else {
                    return $this->render("@App/pdf/voucher_v2.html.twig", array('transfer' => $transfer));
                }
            } else {
                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles())) {
                    $voucherHtml = $this->renderView("@App/pdf/voucherPartnerAgency.html.twig", array('transfer' => $transfer));
                } else {
                    $voucherHtml = $this->renderView("@App/pdf/voucher_v2.html.twig", array('transfer' => $transfer));
                }

                $filepath = $this->container->getParameter('tmp_dir') . "/voucher" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

                $html2Pdf = $this->get('html2pdf_factory')->create();
                $html2Pdf->writeHTML($voucherHtml);
                $html2Pdf->Output($filepath, 'F');

                $response = new Response();
                // Set headers
                $response->headers->set('Cache-Control', 'private');
                $response->headers->set('Content-type', mime_content_type($filepath));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filepath) . '";');
                $response->headers->set('Content-length', filesize($filepath));

                // Send headers before outputting anything
                $response->sendHeaders();

                $response->setContent(file_get_contents($filepath));
                return $response;
            }
        } else {
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/voucher/{transfer}/{type}",name="voucher")
     */
    public function generateVoucher(Transfer $transfer = null, $type = 'pdf')
    {
        if ($transfer != null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()) || $this->getUser() == $transfer->getCreatedBy())) {

            if ($type == 'html') {
                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles())) {
                    return $this->render("@App/pdf/voucherAgent_v2.html.twig", array('transfer' => $transfer));
                } else {
                    return $this->render("@App/pdf/voucher_v2.html.twig", array('transfer' => $transfer));
                }
            } else {
                if (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles())) {
                    if ($transfer->getType() == Transfer::PARTICULAR_COMMAND) {
                        $voucherHtml = $this->renderView("@App/pdf/voucher_particular_command.html.twig", array('transfer' => $transfer));
                    } else {
                        $voucherHtml = $this->renderView("@App/pdf/voucherAgent_v2.html.twig", array('transfer' => $transfer));
                    }
                } else {
                    if ($transfer->getType() == Transfer::PARTICULAR_COMMAND) {
                        $voucherHtml = $this->renderView("@App/pdf/voucher_particular_command.html.twig", array('transfer' => $transfer));
                    } else {
                        $voucherHtml = $this->renderView("@App/pdf/voucher_v2.html.twig", array('transfer' => $transfer));
                    }
                }

                $filepath = $this->container->getParameter('tmp_dir') . "/voucher" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

                $html2Pdf = $this->get('html2pdf_factory')->create();
                $html2Pdf->writeHTML($voucherHtml);
                $html2Pdf->Output($filepath, 'F');

                $response = new Response();
                // Set headers
                $response->headers->set('Cache-Control', 'private');
                $response->headers->set('Content-type', mime_content_type($filepath));
                $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filepath) . '";');
                $response->headers->set('Content-length', filesize($filepath));

                // Send headers before outputting anything
                $response->sendHeaders();

                $response->setContent(file_get_contents($filepath));
                return $response;
            }
        } else {
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/getCalendarParam",name="get_calendar_param",options={"expose"=true})
     */

    public function CalendarParameter(Request $req)
    {
        $transfer = $req->get('typetransfert');
        $param = $this->get('date.parameter.service')->getDayStartParameter($transfer);
        return new JsonResponse($param);
    }


    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/factureSpeciale",name="facture_Speciale")
     */
    public function generatefacturespéciale(Request $req)
    {
        $id = $req->get('id');
        $transfer = $this->getDoctrine()->getRepository('AppBundle:Transfer')->findById($id);
        $transfer = $transfer[0];
        $name = $req->get('name');
        $lastname = $req->get('lastname');
        $adresse = $req->get('adresse');
        $zipcode = $req->get('zipcode');
        $town = $req->get('town');

        $param = array('name' => $name, 'lastname' => $lastname, 'address' => $adresse, 'zipCode' => $zipcode, 'town' => $town);

        if ($transfer != null && (in_array('ROLE_ADMIN', $this->getUser()->getRoles()) || in_array('ROLE_COMMERCIAL', $this->getUser()->getRoles()) || in_array('ROLE_SECRETARY', $this->getUser()->getRoles()) || in_array('ROLE_AGENT', $this->getUser()->getRoles()))) {

            $factureHtml = $this->renderView("@App/pdf/specialfacture.html.twig", array('param' => $param, 'transfer' => $transfer));
            $filepath = $this->container->getParameter('tmp_dir') . "/facture" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->get('html2pdf_factory')->create('L');
            $html2Pdf->writeHTML($factureHtml);
            $html2Pdf->Output($filepath, 'F');

            $response = new Response();
            // Set headers
            $response->headers->set('Cache-Control', 'private');
            $response->headers->set('Content-type', mime_content_type($filepath));
            $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filepath) . '";');
            $response->headers->set('Content-length', filesize($filepath));

            // Send headers before outputting anything
            $response->sendHeaders();

            $response->setContent(file_get_contents($filepath));
            return $response;

        } else {
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }


    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/contactMail",name="contact_Mail")
     */
    public function contactMailAction(Request $request)
    {
        if ($request->getMethod()=='GET'){
            $this->redirectToRoute('contact');
        }

        $nom = $request->get('nom');
        $tel = $request->get('tel');
        $email = $request->get('email');
        $sujet = $request->get('sujet');
        $message = $request->get('message');

        $data = array(
            'nom' => $nom,
            'tel' => $tel,
            'email' => $email,
            'sujet' => $sujet,
            'message' => $message
        );

        //  return $this->render("@App/mails/contact.html.twig", array('data'=>$data));


        $sended = $this->get('transfer.common.service')->sendContactEmail($data);
        if (!$sended) {
            return $this->render('@Twig/Exception/error.html.twig');
        } else {
            return $this->render("@App/static_pages/contact.html.twig");
        }
    }

    /**
     * @param Request $request
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/commande/{transfer}/steps/g4", name="go_straight_to_payement")
     */
    public function sendStraightToFourthStepAction(Request $request, Transfer $transfer)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_ADMIN') || $securityContext->isGranted('ROLE_SECRETARY') || $securityContext->isGranted('ROLE_COMMERCIAL') || $securityContext->isGranted('ROLE_PARTNER_AGENCY')) {
            $sessionTransferToken = md5(time());
            $this->get('session')->set($sessionTransferToken, $transfer);
            $this->get('session')->set('transfer_timestamp_' . $sessionTransferToken, time());
            $partnerAgencies = $this->getDoctrine()
                ->getRepository("AppBundle:AgencePartner")
                ->findAll();

            $relayCustomers = $this->getDoctrine()
                ->getRepository("AppBundle:RelayCustomerDetail")
                ->findAll();

            return $this->render("@App/front/common/fourth_step.html.twig",
                array('token' => $sessionTransferToken,
                    'transfer' => $transfer,
                    'partnerAgencies' => $partnerAgencies,
                    'relayCustomers' => $relayCustomers
                ));

        } else {
            return $this->redirectToRoute('homepage');
        }
    }



}