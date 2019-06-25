<?php
/**
 * Created by PhpStorm.
 * User: omar
 * Date: 31/07/16
 * Time: 22:38
 */

namespace AppBundle\Service\Back;

use AppBundle\Entity\MonthlyInvoice;
use AppBundle\Entity\Transfer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InvoiceService
{
    private $em;
    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container){
        $this->em = $em;
        $this->container = $container;
    }

    public function createMonthlyInvoicesAction()
    {
        $em = $this->container->get('doctrine')->getManager();

        $date = new \DateTime("now");
        $date->format('y-MM-dd');
        $date->modify('-1 month');

        $year = $date->format('Y');
        $month = (int)$date->format('m');

        try{
            $invoices = $this->container->get('doctrine')->getRepository("AppBundle:B2BInvoice")
                ->getInvoicePartnerAgencyB2B($year, $month);

            $res = true;

            foreach($invoices as $invoice){

                if($invoice[0]){
                    $monthlyInvoice = new MonthlyInvoice();

                    $monthlyInvoice->setPartnerAgency($invoice[0]->getPartnerAgency());
                    $monthlyInvoice->setNetPrice($invoice['netPrice_b2b']);
                    $monthlyInvoice->setPrice($invoice['price_b2b']);
                    $monthlyInvoice->setTotalPerson($invoice['totalPerson']);
                    $monthlyInvoice->setYear($year);
                    $monthlyInvoice->setMonth($month);
                    $monthlyInvoice->setState(Transfer::STATUS_WAIT_B2B);

                    $em->persist($monthlyInvoice);
                    $em->flush();

                    if(null != $monthlyInvoice->getId()){
                        $invoiceEntities = $this->container->get('doctrine')->getRepository("AppBundle:B2BInvoice")
                            ->findBy(array(
                                'year'=> $year, 'month'=>$month, 'monthlyB2bEnvoice'=>null, 'partnerAgency'=>$invoice[0]->getPartnerAgency()
                            ));

                        foreach($invoiceEntities as $invoiceEntity){
                            $invoiceEntity->setMonthlyB2bEnvoice($monthlyInvoice);
                            $em->persist($invoiceEntity);
                            $em->flush();
                        }
                    }else $res = false;

                }
            }

            return $res;
        }catch (\Exception $e){
            return false;
        }
    }

}