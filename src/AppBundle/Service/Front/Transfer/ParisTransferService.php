<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 25/06/2016
 * Time: 14:00
 */

namespace AppBundle\Service\Front\Transfer;


use AppBundle\Entity\Transfer;
use Doctrine\ORM\EntityManager;

class ParisTransferService {
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

    public function calculateTarif(Transfer $transfer){

        $qty = $transfer->getQty() ;

        if ($transfer->getQtyChild()){
            $qty += $transfer->getQtyChild();
        }

        $locationPrice = $this->em->getRepository("AppBundle:PorteAPortePrice")->findOneByLocation($transfer->getLocation());
        if (count($locationPrice) == 0){
            return        $result = [
                'prix' => null
            ];
        }
        // var_dump($locationPrice);
        if($qty==1){
            $price = $locationPrice->getPrice()*2;
        }
        else {
            $price = $locationPrice->getPrice()*$qty;
        }

        if($transfer->getRoundTrip())
        {
            $price=$price*2;
        }
        //Todo vérifie s'il y une TVA à appliquer
        $result = [
            'prix' => $price,
            'prixUnitaire' => $locationPrice->getPrice()
        ];
        return $result;
    }
}