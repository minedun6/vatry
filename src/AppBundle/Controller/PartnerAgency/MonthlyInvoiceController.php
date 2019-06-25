<?php

namespace AppBundle\Controller\PartnerAgency;

use AppBundle\Entity\B2BInvoice;
use AppBundle\Entity\MonthlyInvoice;
use AppBundle\Entity\Transfer;
use Doctrine\Tests\Common\DataFixtures\TestEntity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MonthlyInvoiceController
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/part_agency/monthly-invoice")
 */
class MonthlyInvoiceController extends Controller {

    /**
     * @param String $type
     * @param Request $request
     * @return Response
     * @Route("/list/{type}",name="agency_monthly_invoices_list")
     */
    public function indexAction($type = null){
        $created = $this->get('invoices.service')
            ->createMonthlyInvoicesAction();

        $partnerAgency = $this->getUser()->getAgencePartner();

        $date = new \DateTime("now");
        $date->format('y-MM-dd');
        $date->modify('-1 month');

        $year = $date->format('Y');
        $month = (int)$date->format('m');

        $render = "@App/PartnerAgency/Invoices/list.html.twig";

        switch ($type) {
            case 'inProgress' :
                $invoices = $this->getDoctrine()->getRepository("AppBundle:B2BInvoice")
                    ->getAllInvoicePartnerAgencyB2B($year, $month + 1, $partnerAgency);

                $render = "@App/PartnerAgency/Invoices/listInProgress.html.twig";
                break;

            case 'latest' :
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getInvoiceInDate($year, $month, $partnerAgency);
                break;

            case 'historical' :
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getInvoiceNotInDate($year, $month, $partnerAgency);

                break;

            case 'all' :
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getAllInvoiceInDate(null, null, $partnerAgency);

                break;
            default:
                $invoices = $this->getDoctrine()->getRepository("AppBundle:MonthlyInvoice")
                    ->getAllInvoiceInDate(null, null, $partnerAgency);
        }

        return $this->render($render, array(
            'invoices' => $invoices,
            'type' => $type
        ));
    }

    /**
     * @param MonthlyInvoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/invoicedetails/{invoice}", name="agency_invoice_details_invoice")
     */
    public function invoiceDetails(MonthlyInvoice $invoice)
    {
        return $this->render("@App/PartnerAgency/Invoices/invoice_details.html.twig", array(
            'invoice' => $invoice
        ));
    }

    /**
     * @param B2BInvoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/invoiceb2bdetails/{invoice}", name="agency_invoice_b2b_details")
     */
    public function invoiceB2BDetails(B2BInvoice $invoice)
    {
        return $this->render("@App/PartnerAgency/Invoices/invoice_b2b_details.html.twig", array(
            'invoice' => $invoice
        ));
    }

    /**
     * @param MonthlyInvoice $invoice
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list-invoices/{invoice}",name="agency_monthly_invoice_b2b_invoices")
     */
    public function monthlyInvoiceB2BInvoicesList(MonthlyInvoice $invoice = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();

        if(!$invoice)
        {
            $this->addFlash('error', "Facture invalide !");
            return $this->redirectToRoute('agency_monthly_invoices_list');
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
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/latestInvoicedetails", name="agency_latest_invoice_details")
     */
    public function latestInvoiceDetails()
    {
        $partnerAgency = $this->getUser()->getAgencePartner();
        $invoices = $partnerAgency->getMonthlyB2bEnvoices();
        $latestInvoice = $invoices[sizeof($invoices) -1];

        if($latestInvoice)
            return $this->render("@App/PartnerAgency/Invoices/invoice_details.html.twig", array(
                'invoice' => $latestInvoice
            ));
        else  {

            $this->addFlash('error',"Vous n'avez pas encore de facture");
            return $this->redirectToRoute("agency_monthly_invoices_list");
        }


    }

}