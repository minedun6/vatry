<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 04/05/2016
 * Time: 20:19
 */

namespace AppBundle\Service\Front\Transfer;


use AppBundle\Entity\Flight;
use AppBundle\Entity\Location;
use AppBundle\Entity\Transfer;
use AppBundle\Utilities\Helpers;
use Doctrine\ORM\EntityManager;

class GareTransferService {

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

    public function calculateTarif(Transfer $transfer, $usertype = "customer" ){

        $prix1 = 0 ;
        $prix2 = 0;

        $qtychild=0;
        $qtybaby=0;
        $unitaryadultprice = 0;
        $childrenprice = 0;
        $babiesprice = 0;
        $price=0;
        $duration = 60;

        $qtyadult = $transfer->getQty();

        if ($transfer->getQtyChild()){
            $qtychild = $transfer->getQtyChild();
        }

        if ($transfer->getQtyBaby()){
            $qtybaby = $transfer->getQtyBaby();
        }

        $Gareprice = $this->em->getRepository("AppBundle:InterVillePrices")
            ->findOneBy(array('location'=> $transfer->getLocation()));

        if ($Gareprice){
            if($usertype==="partneragency"){

                $currentuser="partneragency";   //used to test for the result return

                switch($qtyadult){
                    case 1:
                        $adultprice = $Gareprice->getAgencyadultePrice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getAgencyadultePrice();
                        break;
                    case 2:
                        $adultprice = $Gareprice->getAgencytwoadultsPrice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getAgencytwoadultsPrice();
                        break;
                    case 3:
                        $adultprice = $Gareprice->getAgencythreeadultsPrice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getAgencythreeadultsPrice();
                        break;
                    default:
                        $adultprice = $Gareprice->getAgencythreeadultsPrice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getAgencythreeadultsPrice();
                }

                $childrenprice = $Gareprice->getAgencychildPrice()*$qtychild;
                $babiesprice = $Gareprice->getAgencybabyPrice()*$qtybaby;

                $price+=$adultprice+$childrenprice+$babiesprice;
            }


            else {
                $currentuser="customer";

                switch($qtyadult){
                    case 1:
                        $adultprice = $Gareprice->getAdulteprice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getAdulteprice();
                        break;
                    case 2:
                        $adultprice = $Gareprice->getTwoadultsPrice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getTwoadultsPrice();
                        break;
                    case 3:
                        $adultprice = $Gareprice->getThreeadultsPrice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getThreeadultsPrice();
                        break;
                    default:
                        $adultprice = $Gareprice->getThreeadultsPrice()*$qtyadult;
                        $unitaryadultprice=$Gareprice->getThreeadultsPrice();
                }

                $childrenprice = $Gareprice->getChildPrice()*$qtychild;
                $babiesprice = $Gareprice->getBabyPrice()*$qtybaby;

                $price+=$adultprice+$childrenprice+$babiesprice;
            }

            $prix1 = $price;
            $duration = $Gareprice->getDuration();
        }

        if ($transfer->getRoundTrip()){
            $prix2 = $prix1 ;
        }


        //on renvoi le prix unitaire et le prix total calculé
        $result = [
            'prix' => $prix1 + $prix2,
            'prixAdulte' => $unitaryadultprice,
            'prixEnfant' => $childrenprice,
            'rdv'=>'RDV',
            'duration' => $duration
        ];
        return $result;
    }

    public function getPickupTime(Transfer $transfer,$direction,$return = false){

        if ($return){
            if ($direction == Transfer::FROM_VATRY){
                $direction = Transfer::TO_VATRY;
            }else{
                $direction = Transfer::FROM_VATRY;
            }
        }
        if($return){
            $thisFlight=$transfer->getFlight2();
        }else{
            $thisFlight=$transfer->getFlight();
        }


        if ($direction == Transfer::FROM_VATRY){
            $date = Helpers::createDateTimeByMinutes($thisFlight->getTime(),35);
            }else{
            if ($transfer->getLocation()->getZipCode() == Location::GARE_CHALONS){
                $date = Helpers::createDateTimeByMinutes($thisFlight->getTime(),-140);
            }else{
                $date = Helpers::createDateTimeByMinutes($thisFlight->getTime(),-190);
            }
        }
        return $date ;
    }
}