<?php

namespace AppBundle\Controller\AgentAdmin;

use AppBundle\Entity\User;
use AppBundle\Entity\AgencePartner;
use AppBundle\Entity\B2BInvoice;
use AppBundle\Entity\MonthlyInvoice;
use AppBundle\Entity\Transfer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\DateTime;
/**
 * Class InvoiceController
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/agent-admin/invoice")
 */
class InvoiceController extends Controller {

    /**
     * @Route("/list",name="b2b_invoices_list")
     */
    public function indexAction(){

        $render = "@App/Back/Invoices/list.html.twig";
        $parent = $this->get('common.back.service')->getMenuExtends();


        $invoices = $this->getDoctrine()->getRepository("AppBundle:B2BInvoice")
            ->getAllInvoicePartnerAgencyB2B();
//        dump($invoices);

//        foreach($invoices as $invoice){
//            dump($invoice['price_b2b']);
//            dump($invoice['netPrice_b2b']);
//            dump($invoice[0]->getPartnerAgency());
//        }

        return $this->render($render, array(
            'invoices' => $invoices,
            'parent' => $parent
        ));
    }

}