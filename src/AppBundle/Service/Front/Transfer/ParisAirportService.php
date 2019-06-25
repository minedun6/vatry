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

class ParisAirportService
{

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function calculateTarif(Transfer $transfer)
    {

        $prix1 = 0;
        $prix2 = 0;

        $prixAdulte = 0;
        $prixChild = 0;

        $price = $this->em->getRepository("AppBundle:InterVillePrices")
            ->findOneBy(array('location' => $transfer->getLocation()));

        if ($price) {
            $prixAdulte = $price->getAdultePrice();
            $prixChild = $price->getChildPrice();

            $prix1 = floatval($price->getAdultePrice()) * intval($transfer->getQty());
            $prix1 += floatval($price->getChildPrice()) * intval($transfer->getQtyChild());
        }

        if ($transfer->getRoundTrip()) {
            $prix2 = $prix1;
        }


        //on renvoi le prix unitaire et le prix total calculé
        $result = [
            'prix' => $prix1 + $prix2,
            'prixAdulte' => $prixAdulte,
            'prixEnfant' => $prixChild,
            'rdv' => 'RDV'
        ];
        return $result;
    }

    public function getPickupTime(Transfer $transfer, $direction, $return = false)
    {

        if ($return) {
            if ($direction == Transfer::FROM_VATRY) {
                $direction = Transfer::TO_VATRY;
            } else {
                $direction = Transfer::FROM_VATRY;
            }
        }
        if ($return) {
            $thisFlight = $transfer->getFlight2();
        } else {
            $thisFlight = $transfer->getFlight();
        }


        if ($direction == Transfer::FROM_VATRY) {
            $date = Helpers::createDateTimeByMinutes($thisFlight->getTime(), 35);
        } else {
            $date = Helpers::createDateTimeByMinutes($thisFlight->getTime(), -240);
        }
        return $date;
    }
}