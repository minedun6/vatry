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

class InterVilleTransferService {

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager,$startNightHour,$endNightHour,$nightPercent){
        $this->em = $entityManager;
        $this->startNightHour = $startNightHour;
        $this->endNightHour = $endNightHour;
        $this->nightPercent= $nightPercent;
    }

    public function calculateTarif(Transfer $transfer,$usertype,$applyNightTarif = false){

        $qtychild=0;
        $qtybaby=0;
        $price=0;

        $qtyadult = $transfer->getQty();

        if ($transfer->getQtyChild()){
          $qtychild = $transfer->getQtyChild();
        }

        if ($transfer->getQtyBaby()){
          $qtybaby = $transfer->getQtyBaby();
        }


        $interVilleprice = $this->em->getRepository("AppBundle:InterVillePrices")->findOneByZipCode($transfer->getLocation()->getZipCode());
        if (count($interVilleprice) == 0){
            return        $result = [
                'prix' => null
            ];
        }


        //$price+=($interVilleprice->getAdultePrice())*($transfer->getQty());
        //$price+=($interVilleprice->getChildPrice())*($transfer->getQtyChild());


        if($usertype==="partneragency"){

            $currentuser="partneragency";   //used to test for the result return

          switch($qtyadult){
            case 1:
              $adultprice = $interVilleprice->getAgencyadultePrice()*$qtyadult;
              $unitaryadultprice=$interVilleprice->getAgencyadultePrice();
                break;
            case 2:
              $adultprice = $interVilleprice->getAgencytwoadultsPrice()*$qtyadult;
              $unitaryadultprice=$interVilleprice->getAgencytwoadultsPrice();
                break;
            case 3:
              $adultprice = $interVilleprice->getAgencythreeadultsPrice()*$qtyadult;
              $unitaryadultprice=$interVilleprice->getAgencythreeadultsPrice();
                break;
            default:
               $adultprice = $interVilleprice->getAgencythreeadultsPrice()*$qtyadult;
               $unitaryadultprice=$interVilleprice->getAgencythreeadultsPrice();
        }

            $childrenprice = $interVilleprice->getAgencychildPrice()*$qtychild;
            $babiesprice = $interVilleprice->getAgencybabyPrice()*$qtybaby;

            $price+=$adultprice+$childrenprice+$babiesprice;
        }


        else {
         $currentuser="customer";

          switch($qtyadult){
            case 1:
              $adultprice = $interVilleprice->getAdulteprice()*$qtyadult;
              $unitaryadultprice=$interVilleprice->getAdulteprice();
                break;
            case 2:
              $adultprice = $interVilleprice->getTwoadultsPrice()*$qtyadult;
              $unitaryadultprice=$interVilleprice->getTwoadultsPrice();
                break;
            case 3:
              $adultprice = $interVilleprice->getThreeadultsPrice()*$qtyadult;
              $unitaryadultprice=$interVilleprice->getThreeadultsPrice();
                break;
            default:
               $adultprice = $interVilleprice->getThreeadultsPrice()*$qtyadult;
               $unitaryadultprice=$interVilleprice->getThreeadultsPrice();
        }

            $childrenprice = $interVilleprice->getChildPrice()*$qtychild;
            $babiesprice = $interVilleprice->getBabyPrice()*$qtybaby;

            $price+=$adultprice+$childrenprice+$babiesprice;
        }

        $goPrice = $price;
        $returnPrice = 0 ;






        if ($transfer->getRoundTrip()){
            $interVilleprice2 = $this->em->getRepository("AppBundle:InterVillePrices")->findOneByZipCode($transfer->getLocation2()->getZipCode());

        if (count($interVilleprice2) == 0){
            return        $result = [
                'prix' => null
            ];
        }


        //$price+=($interVilleprice2->getAdultePrice())*($transfer->getQty());
        //$price+=($interVilleprice2->getChildPrice())*($transfer->getQtyChild());

        if($usertype==="partneragency"){
          $currentuser="partneragency";
          switch($qtyadult){
            case 1:              
              $adultprice = $interVilleprice2->getAgencyadultePrice()*$qtyadult;
              //$unitaryadultprice2=$interVilleprice2->getAgencyadultePrice();
                break;
            case 2:
              $adultprice = $interVilleprice2->getAgencytwoadultsPrice()*$qtyadult;
              //$unitaryadultprice2=$interVilleprice2->getAgencytwoadultsPrice();
                break;
            case 3:
              $adultprice = $interVilleprice2->getAgencythreeadultsPrice()*$qtyadult;
              //$unitaryadultprice2=$interVilleprice2->getAgencythreeadultsPrice();
                break;
            default:
               $adultprice = $interVilleprice2->getAgencythreeadultsPrice()*$qtyadult;
               //$unitaryadultprice2=$interVilleprice2->getAgencythreeadultsPrice();
        }

            $childrenprice = $interVilleprice2->getAgencychildPrice()*$qtychild;
            $babiesprice = $interVilleprice2->getAgencybabyPrice()*$qtybaby;

            $price+=$adultprice+$childrenprice+$babiesprice;
        }


        else {
          $currentuser="customer";
          switch($qtyadult){
            case 1:
              $adultprice = $interVilleprice2->getAdulteprice()*$qtyadult;
              //$unitaryadultprice2=$interVilleprice2->getAdulteprice();
                break;
            case 2:
              $adultprice = $interVilleprice2->getTwoadultsPrice()*$qtyadult;
                //$unitaryadultprice2=$interVilleprice2->getTwoadultsPrice();
                break;
            case 3:
              $adultprice = $interVilleprice2->getThreeadultsPrice()*$qtyadult;
              //$unitaryadultprice2=$interVilleprice2->getThreeadultsPrice();
                break;
            default:
               $adultprice = $interVilleprice2->getThreeadultsPrice()*$qtyadult;
               //$unitaryadultprice2=$interVilleprice2->getThreeadultsPrice();
        }

            $childrenprice = $interVilleprice2->getChildPrice()*$qtychild;
            $babiesprice = $interVilleprice2->getBabyPrice()*$qtybaby;

            $price+=$adultprice+$childrenprice+$babiesprice;
        }


            $returnPrice = $price - $goPrice ;


        }


        $extra = 0 ;
        if ($applyNightTarif){
            if ($transfer->getPickupDate() != null ){
                $hour = intval($transfer->getPickupDate()->format('H'));
                if (($hour <= 23 && $hour >= $this->startNightHour  )
                    || ($hour >= 0 && $hour <= $this->endNightHour)){
                    $extra += ($goPrice * $this->nightPercent / 100 ) ;
                }
            }

            if ($transfer->getRoundTrip() && $transfer->getPickupDate2() != null ){
                $hour = intval($transfer->getPickupDate2()->format('H'));
                if (($hour <= 23 && $hour >= $this->startNightHour  )
                    || ($hour >= 0 && $hour <= $this->endNightHour)){
                    $extra += ($returnPrice * $this->nightPercent / 100 ) ;
                }
            }
        }

        $price += $extra ;

        if($currentuser==="partneragency")
        {


        $result = [
            'prix' => $price,
            'prixenfant' => $childrenprice,
            'prixbebe' => $babiesprice,
            'prixadulte' => $adultprice,
            'prixUnitaireEnfant' => $interVilleprice->getAgencychildPrice(),
            'prixUnitaireBebe' => $interVilleprice->getAgencybabyPrice(),
            'prixUnitaire' => $unitaryadultprice,
            'rdv'=>$interVilleprice->getRdv(),
            'duration' => $interVilleprice->getDuration()
       ];

        }

      else {

        $result = [
           'prix' => $price,
           'prixenfant' => $childrenprice,
           'prixbebe' => $babiesprice,
           'prixadulte' => $adultprice,
           'prixUnitaireEnfant' => $interVilleprice->getChildPrice(),
           'prixUnitaireBebe' => $interVilleprice->getBabyPrice(),
           'prixUnitaire' => $unitaryadultprice,
           'rdv'=>$interVilleprice->getRdv(),
           'duration' => $interVilleprice->getDuration()
       ];

      }

        return $result;
    }

}
