<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 16/04/2016
 * Time: 21:25
 */

namespace AppBundle\Service\Front\Transfer;


use AppBundle\Entity\Transfer;
use Doctrine\ORM\EntityManager;

class PrivateTransferService {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var int
     */
    private $startNightHour;

    /**
     * @var int
     */
    private $endNightHour;

    /**
     * @var float
     */
    private $nightPercent;

    public function __construct(EntityManager $entityManager,$startNightHour,$endNightHour,$nightPercent){
        $this->em = $entityManager;
        $this->startNightHour = $startNightHour;
        $this->endNightHour = $endNightHour;
        $this->nightPercent= $nightPercent;
    }

    public function calculateTarif(Transfer $transfer,$applyNightTarif = false){

        $qty = $transfer->getQty() ;
        if ($transfer->getQtyChild()){
            $qty += $transfer->getQtyChild();
        }

        $locationPrice = $this->em->getRepository("AppBundle:PrivateLocationPrice")->findPriceByQty($transfer->getLocation(),$qty);
        if (count($locationPrice) == 0){
            return null;
        }

        $goPrice = $locationPrice[0]->getPrice();
        $price = $locationPrice[0]->getPrice();
        $returnPrice = 0 ;
        if ($transfer->getRoundTrip()){
            $locationPrice2 = $this->em->getRepository("AppBundle:PrivateLocationPrice")->findPriceByQty($transfer->getLocation2(),$qty);
            if (count($locationPrice2) > 0){
                $returnPrice = $locationPrice2[0]->getPrice();
                $price += $locationPrice2[0]->getPrice();
            }
        }

        $extra = 0 ;
        $extraGoPrice = 0;
        $extraReturnPrice = 0;
        if ($applyNightTarif){
            if ($transfer->getPickupDate() != null ){
                $hour = intval($transfer->getPickupDate()->format('H'));
                if (($hour <= 23 && $hour >= $this->startNightHour  )
                    || ($hour >= 0 && $hour <= $this->endNightHour)){
                    $extraGoPrice = ($goPrice * $this->nightPercent / 100 ) ;
                    $extra += ($goPrice * $this->nightPercent / 100 ) ;
                }
            }

            if ($transfer->getRoundTrip() && $transfer->getPickupDate2() != null ){
                $hour = intval($transfer->getPickupDate2()->format('H'));
                if (($hour <= 23 && $hour >= $this->startNightHour  )
                    || ($hour >= 0 && $hour <= $this->endNightHour)){
                    $extraReturnPrice = ($returnPrice * $this->nightPercent / 100 ) ;
                    $extra += ($returnPrice * $this->nightPercent / 100 ) ;
                }
            }
        }
        $net = $goPrice + $returnPrice;
        $price += $extra ;

        return [
            'price' => $price,
            'net' => $net,
            'aller' => [
                'netAller' => $goPrice,
                'extraAller' => $extraGoPrice
            ],
            'retour' => [
                'netRetour' => $returnPrice,
                'extraRetour' => $extraReturnPrice
            ],
            'extra' => $extra
        ];
    }

}