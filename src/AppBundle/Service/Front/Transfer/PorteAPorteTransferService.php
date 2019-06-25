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

class PorteAPorteTransferService {

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager){
        $this->em = $entityManager;
    }

    public function calculateTarif(Transfer $transfer,$usertype){

            $qtychild=0;
            $qtybaby=0;

        $qtyadult = $transfer->getQty();

        if ($transfer->getQtyChild()){
            $qtychild = $transfer->getQtyChild();
        }

        if ($transfer->getQtyBaby()){
            $qtybaby = $transfer->getQtyBaby();
        }



        $locationPrice = $this->em->getRepository("AppBundle:PorteAPortePrice")->findOneByLocation($transfer->getLocation());
        if (count($locationPrice) == 0){
            return        $result = [
                'prix' => null
            ];
        }


        if($usertype==="partneragency"){
          switch($qtyadult){
            case 1:
              $adultprice = $locationPrice->getAgencyprice()*$qtyadult;
              $unitaryadultprice=$locationPrice->getAgencyprice();
                break;
            case 2:
              $adultprice = $locationPrice->getAgencytwoadultsPrice()*$qtyadult;
              $unitaryadultprice=$locationPrice->getAgencytwoadultsPrice();
                break;
            case 3:
              $adultprice = $locationPrice->getAgencythreeadultsPrice()*$qtyadult;
              $unitaryadultprice=$locationPrice->getAgencythreeadultsPrice();
                break;
            default:
               $adultprice = $locationPrice->getAgencythreeadultsPrice()*$qtyadult;
               $unitaryadultprice=$locationPrice->getAgencythreeadultsPrice();
        }

            $childrenprice = $locationPrice->getAgencychildPrice()*$qtychild;
            $babiesprice = $locationPrice->getAgencybabyPrice()*$qtybaby;

            $totalprice=$adultprice+$childrenprice+$babiesprice;

            $result = [
                'prix' => $totalprice,
                'prixenfant' => $childrenprice,
                'prixbebe' => $babiesprice,
                'prixadulte' => $adultprice,
                'prixUnitaireEnfant' => $locationPrice->getAgencychildPrice(),
                'prixUnitaireBebe' => $locationPrice->getAgencybabyPrice(),
                'prixUnitaire' => $unitaryadultprice
            ];

        }


        else {

          switch($qtyadult){
            case 1:
              $adultprice = $locationPrice->getPrice()*$qtyadult;
              $unitaryadultprice=$locationPrice->getPrice();
                break;
            case 2:
              $adultprice = $locationPrice->getTwoadultsPrice()*$qtyadult;
                $unitaryadultprice=$locationPrice->getTwoadultsPrice();
                break;
            case 3:
              $adultprice = $locationPrice->getThreeadultsPrice()*$qtyadult;
              $unitaryadultprice=$locationPrice->getThreeadultsPrice();
                break;
            default:
               $adultprice = $locationPrice->getThreeadultsPrice()*$qtyadult;
               $unitaryadultprice=$locationPrice->getThreeadultsPrice();
        }

            $childrenprice = $locationPrice->getChildPrice()*$qtychild;
            $babiesprice = $locationPrice->getBabyPrice()*$qtybaby;

            $totalprice=$adultprice+$childrenprice+$babiesprice;

            $result = [
                'prix' => $totalprice,
                'prixenfant' => $childrenprice,
                'prixbebe' => $babiesprice,
                'prixadulte' => $adultprice,
                'prixUnitaireEnfant' => $locationPrice->getChildPrice(),
                'prixUnitaireBebe' => $locationPrice->getBabyPrice(),
                'prixUnitaire' => $unitaryadultprice
            ];

        }



        return $result;
    }

}
