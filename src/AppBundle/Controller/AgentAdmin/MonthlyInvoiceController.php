<?php

namespace AppBundle\Controller\AgentAdmin;

use AppBundle\Entity\B2BInvoice;
use AppBundle\Entity\MonthlyInvoice;
use AppBundle\Entity\Transfer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MonthlyInvoiceController
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/agent-admin/monthly-invoice")
 */
class MonthlyInvoiceController extends Controller {

    /**
     * @param String $type
     * @param Request $request
     * @return Response
     * @Route("/list/{type}",name="monthly_invoices_list")
     */
    public function indexAction($type = null){
        $parent = $this->get('common.back.service')->getMenuExtends();
        $created = $this->get('invoices.service')
            ->createMonthlyInvoicesAction();

        $date = new \DateTime("now");
        $date->format('y-MM-dd');
        $date->modify('-1 month');

        $year = $date->format('Y');
        $month = (int)$date->format('m');

        switch ($type) {
            case 'latest' :
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getInvoiceInDate($year, $month);
                break;

            case 'historical' :
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getInvoiceNotInDate($year, $month);

                break;

            case 'all' :
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getAllInvoiceInDate();

                break;
            default:
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getAllInvoiceInDate();
        }

        $render = "@App/Back/Invoices/list.html.twig";


        return $this->render($render, array(
            'invoices' => $invoices,
            'type' => $type,
            'parent' => $parent
        ));
    }



    /**
     * @param Request $request
     * @return Response
     * @Route("/payed", name="invoice_payed_state")
     */
    public function payedInvoice(Request $request)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if($request->getMethod()  == 'POST'){
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('invoice');
            $monthlyInvoice = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")->find($id);

            if (!$monthlyInvoice){
                $this->addFlash('error',"Facture inexistante");
                $this->addFlash('error',"Echec lors du paiement de la facture");
            }else{
                $modePay = $request->request->get('modePay');
                $cbRef = $request->request->get('cbRef');

                if(($modePay == Transfer::TYPE_CREDIT_CARD || $modePay == Transfer::TYPE_CHEQUE || $modePay == Transfer::TYPE_VIREMENT ) && (!$cbRef || $cbRef == "" || $cbRef == null)){
                    $this->addFlash('error',"La référence du paiement est obligatoire");
                }
                else{
                    $monthlyInvoice->setState(MonthlyInvoice::STATUS_PAID_B2B);

                    $em->flush();

                    $invoices = $monthlyInvoice->getB2bEnvoices();

                    foreach($invoices as $invoice){

                        $transfert = $invoice->getTransfer();
                        $transfert->setStatus(Transfer::STATUS_PAID_B2B);

                        $payment = $invoice->getTransfer()->getPayment();
                        $payment->setType($modePay);

                        if($modePay == Transfer::TYPE_CREDIT_CARD || $modePay == Transfer::TYPE_CHEQUE || $modePay == Transfer::TYPE_VIREMENT )
                            $payment->setCreditCardReference($cbRef);

                        $em->flush();
                    }

                    $this->addFlash('success','Facture payé avec succès');
                }
            }
        }

        return $this->redirectToRoute("monthly_invoices_list", array('type'=>'latest',
            'parent'=>$parent
        ) );

    }

    /**
     * @param MonthlyInvoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/agencydetails/{invoice}", name="invoice_details_agency")
     */
    public function invoiceAgencyDetails(MonthlyInvoice $invoice)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();

        return $this->render("@App/Back/Invoices/invoice_agency_details.html.twig", array(
            'parent' => $parent,
            'invoice' => $invoice,
        ));
    }

    /**
     * @param MonthlyInvoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list-invoices/{invoice}",name="list_monthly_invoice_b2b_invoices")
     */
    public function monthlyInvoiceB2BInvoicesList(MonthlyInvoice $invoice = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();

        if(!$invoice)
        {
            $this->addFlash('error', "Facture invalide !");
            return $this->redirectToRoute('monthly_invoices_list');
        }

        $transfers = $this->getDoctrine()->getRepository('AppBundle:Transfer')->getTransfertMonthlyInvoice($invoice);

        //dump($transfers);die();
        return $this->render("@App/Agent/Transfers/listeTransfertsMonthlyInvoice.html.twig", array(
            'transfers' => $transfers,
            'b2b' => 'b2b',
            'parent' => $parent
        ));
    }

    /**
     * @param MonthlyInvoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/invoicedetails/{invoice}", name="invoice_details_invoice")
     */
    public function invoiceDetails(MonthlyInvoice $invoice)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();

        return $this->render("@App/Back/Invoices/invoice_details.html.twig", array(
            'parent' => $parent,
            'invoice' => $invoice,
        ));
    }

    /**
     * @Route("/create_monthly",name="create_monthly_invoice")
     */
    public function createMonthlyInvoicesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $created = $this->get('invoices.service')
            ->createMonthlyInvoicesAction();
        // dump($created);

        // die();
    }

}