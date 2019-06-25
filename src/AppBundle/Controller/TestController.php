<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 09/06/2016
 * Time: 23:34
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Transfer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller {

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/facture_test/{transfer}/{type}")
     */
    public function testFacture(Transfer $transfer,$type = 'pdf')
    {

        if ($type == 'html') {

            return $this->render("@App/pdf/facture.html.twig", array('transfer' => $transfer));

        } else {
//            $voucherHtml = $this->renderView("@App/pdf/facture.html.twig", array('transfer' => $transfer));
            $voucherHtml = $this->renderView("@App/pdf/facturePartnerAgency.html.twig", array('transfer' => $transfer));

            $filepath = $this->container->getParameter('tmp_dir') . "/facture" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->get('html2pdf_factory')->create('L');
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
    }

    /**
     * @param Transfer $transfer
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/voucher_test/{transfer}/{type}")
     */
    public function testVoucher(Transfer $transfer,$type = 'pdf')
    {

        if ($type == 'html') {

            return $this->render("@App/pdf/voucher_v2.html.twig", array('transfer' => $transfer));

        } else {
            $voucherHtml = $this->renderView("@App/pdf/voucher_v2.html.twig", array('transfer' => $transfer));

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
    }


    /**
     * @param $error
     * @return Response
     * @Route("/test_error/{error}")
     */
    public function testError($error=null){

            if ($error == 404){
                return  $this->render('@Twig/Exception/error404.html.twig');
            }

            if ($error == 403){
                return  $this->render('@Twig/Exception/error403.html.twig');
            }

            return  $this->render('@Twig/Exception/error.html.twig');

        }

    /**
     * @Route("/simulate_payment_form")
     * @Method("post")
     */
    public function testPayment(Request $request){

        $data = $request->request->getIterator();

        $ref = $data['reference'];
        $transfer = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findOneBy(array(
            'referenceWithBank'=> $ref
        ));

        return $this->render("@App/front/common/simulate_payment.html.twig",array(
            'transfer' => $transfer
        ));
    }

    /**
     * @param Transfer $transfer
     * @param $yesNo
     * @Route("/simulate_payment/{transfer}/{yesNo}",name="simulate_payment_test")
     * @return Response
     */
    public function paymentOk(Transfer $transfer,$yesNo){

        if ($transfer->getStatus() != Transfer::STATUS_OPEN){
            $yesNo = null;
        }else{

            if ($yesNo == '1'){
                $transfer->setStatus(Transfer::STATUS_PAID_TEST);
            }else{
                $transfer->setStatus(Transfer::STATUS_PAID_CANCELED);
            }
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render("@App/front/common/simulate_payment.html.twig",array(
            'transfer' => $transfer,
            'yesNo' => $yesNo
        ));
    }

    /**
     * @param Transfer $transfer
     * @Route("/testMail/{transfer}",name="mail_test")
     * @return Response
     */
    public function testMail(Transfer $transfer){
      //  var_dump($transfer);die();

        $sended = $this->get('transfer.common.service')->sendConfirmationForLaterPayment($transfer);
        if (!$sended) {
            return $this->render('@Twig/Exception/error.html.twig');
        }
        else{
            return        $this->render("@App/mails/pending_confirmation_payment.html.twig",array("transfer"=>$transfer));
        }


        }
}