<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 25/07/2016
 * Time: 15:43
 */

namespace AppBundle\Service\Back;

use AppBundle\Entity\Transfer;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class StatisticsService
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    public static function getSourceName($userType)
    {
        switch($userType) {
            case User::TYPE_AGENT:{
                return 'Agents NDV';
            }
            case User::TYPE_ADMIN:{
                return 'Agents NDV';
            }
            case User::TYPE_AGENT_ADMIN:{
                return 'Agents NDV';
            }
            case User::TYPE_SECRETARY:{
            return 'Agents NDV';
            }
            case User::TYPE_CUSTOMER:{
                return 'Web NDV';
            }
            case User::TYPE_RELAY_CUSTOMER:{
                return 'Web NDV';
            }
            case User::TYPE_PARTNERAGENCY:{
                return 'Agence de voyages';
            }
            case User::TYPE_PARTNER:{
                return 'Partenaire';
            }
            default:return 'Autres';
        }
    }
    public function getStatsByPickUpOrDropLocationOfPassengerNumber($startdate, $endate,$sens,$sens2)
    {

        $pickup=[];
        $drop=[];
        $data = $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerPickUpOrDropLocation($startdate, $endate , $sens);
        $data2= $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPickupOrDropOfReturnTransfers($startdate,$endate , $sens2);
        /* foreach ($data2 as &$dt2) {

         /*  if ($dt2['direction'] == Transfer::FROM_VATRY) {
                 $dt2['direction']=Transfer::TO_VATRY;
             }
             else
                 {
                     $dt2['direction']=Transfer::FROM_VATRY;
                 }
         }*/
        $data=array_merge($data,$data2);
/*        $pickup['Vatry'] = 0;
        $drop['Vatry'] = 0;*/

        $total = 0;
        foreach ($data as $dt) {
            $name =str_replace("'"," ",$dt['name']);
            $total = $total + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            if ($dt['direction'] == Transfer::FROM_VATRY) {

                if (!isset($drop[$name])) {
                    $drop[$name] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                } else {
                    $drop[$name] = $drop[$name] + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                }
             //   $pickup['Vatry'] = $pickup['Vatry'] + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            } else {
                if (!isset($pickup[$name])) {
                    $pickup[$name] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                } else {
                    $pickup[$name] = $pickup[$name] + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                }
             //   $drop['Vatry'] = $drop['Vatry'] + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            }

        }


        return array('drop' => $drop, 'pickup' => $pickup, 'total' => $total, 'data' => $data);
    }

    public function getStatsByPrestationTypeOfPassengerNumber($startdate, $endate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerPrestationType($day1, $day2);
        $data2 = $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerPrestationTypeOfReturnTransfers($day1, $day2);
        $data=array_merge($data,$data2);
        $total=0;
        $result=null;
        foreach ($data as $dt) {
            $total = $total + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            switch ($dt['type']) {
                case Transfer::INTERVILLE_TRANSFERT_To_TOWN: {
                    if (!isset($result['Partagée Interville'])) {

                        $result['Partagée Interville'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Partagée Interville'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }

                }
                case Transfer::PORTEAPORTE_TRANSFERT_To_TOWN: {
                    if (!isset($result['Partagée Aéroport-Domicile'])) {
                        $result['Partagée Aéroport-Domicile'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Partagée Aéroport-Domicile'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }
                }
                case Transfer::GARE_TRANSFER: {
                    if (!isset($result['Aéroport-Gare'])) {
                        $result['Aéroport-Gare'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Aéroport-Gare'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }
                }
                case Transfer::PARIS_TRANSFER: {
                    if (!isset($result['Aéroport-Région Parisienne'])) {
                        $result['Aéroport-Région Parisienne'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Aéroport-Région Parisienne'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }
                }
                case Transfer::PRIVATE_TRANSFER_TO_AIRPORT: {
                    if (!isset($result['Privé Aéroport-Aéroport'])) {
                        $result['Privé Aéroport-Aéroport'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Privé Aéroport-Aéroport'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }
                }
                case Transfer::PRIVATE_TRANSFER_TO_TOWN: {
                    if (!isset($result['Privé Aéroport-Domicile'])) {
                        $result['Privé Aéroport-Domicile'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Privé Aéroport-Domicile'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }
                }

                case Transfer::PARTICULAR_COMMAND: {
                    if (!isset($result['Commande particulière'])) {
                        $result['Commande particulière'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Commande particulière'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }
                }

                case Transfer::PARIS_AIRPORT: {
                    if (!isset($result['Paris Aéroport'])) {
                        $result['Paris Aéroport'] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    } else {
                        $result['Paris Aéroport'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
                        break;
                    }
                }

                default:
                    break;
            }
            //sizeof($result)
        }
       return array('prestations' => $result, 'total' => $total , 'data' => $data);
    }

    public function getStatsByTransferWayOfPassengerNumber($startdate, $endate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerTransferWay($day1, $day2);
        $data2= $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerTransferWayOfReturnTransfers($day1,$day2);

        foreach ($data2 as &$dt2) {

            if ($dt2['direction'] == Transfer::FROM_VATRY) {
                $dt2['direction']=Transfer::TO_VATRY;
            }
            else
            {
                $dt2['direction']=Transfer::FROM_VATRY;
            }
        }

        $data=array_merge($data,$data2);

        $result['De Vatry'] = 0;
        $result['Vers Vatry'] = 0;
        foreach ($data as $dt) {

            if ($dt['direction'] == Transfer::FROM_VATRY)
                $result['De Vatry'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            else
                $result['Vers Vatry'] += $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
        }
        return array('transferway' => $result ,'total' => $result['De Vatry']+$result['Vers Vatry'] ,'data' => $data);
    }


    public function getStatsByFlightDestinationOfPassengerNumber($startdate, $endate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerFlightsDestination($day1, $day2);
        $data2= $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerFlightDestinationOfReturnTransfers($day1, $day2);
        $data=array_merge($data,$data2);
        $total=0;
        $result=null;
        foreach ($data as $dt) {
            $toLocation =str_replace("'"," ",$dt['toLocation']);
            $total+=$dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            if (!isset($result[$toLocation])) {
                $result[$toLocation] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            } else {
                $result[$toLocation] = $result[$toLocation] + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            }
        }
        return array('data' => $data , 'total' => $total , 'destinationslvol' => $result);
    }
    public function getStatsByFlightOriginOfPassengerNumber($startdate, $endate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerFlightsOrigin($day1, $day2);
        $data2= $this->em->getRepository('AppBundle:Transfer')->getPassengersNumberPerFlightOriginOfReturnTransfers($day1, $day2);
        $data=array_merge($data,$data2);
        $result = array();
        $total=0;
        foreach ($data as $dt) {
            $fromLocation =str_replace("'"," ",$dt['fromLocation']);
            $total+=$dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            if (!isset($result[$fromLocation])) {
                $result[$fromLocation] = $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            } else {
                $result[$fromLocation] = $result[$fromLocation] + $dt['qty'] + $dt['qtyChild'] + $dt['qtyBaby'];
            }
        }
        return array('data' => $data , 'total' => $total , 'originvol' => $result);
    }
    public function getStatsByOrdersSource($startdate, $endate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getPaidTransfers($day1, $day2);
        $data=array_map(function ($value) {
            if($value)
            return [
                'id' => $value->getId(),
                'date' => $value->getPickupDate(),
                'type' => ($value->getAffectedTo()&&$value->getCreatedBy()!=$value->getAffectedTo())?StatisticsService::getSourceName($value->getAffectedTo()->getType()):StatisticsService::getSourceName($value->getCreatedBy()->getType()),
                'partner' => $value->getPartner()?$value->getPartner()->getName():null
                   ];
        }, $data);

        $result = array_count_values(array_map(function ($value) {
            if($value['partner'])
                return $value['partner'];
              else
                return $value['type'];
        },$data));
        return array('ordersource' => $result , 'data' => $data,'total' => sizeof($result));
    }

    public function getStatsByPrestationTypeOfOrdersNumber($startdate, $endate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getOrdersNumberPerPrestationType($day1, $day2);
        $result = array_count_values(array_map(function ($value) {
            switch($value['type']) {
                case Transfer::INTERVILLE_TRANSFERT_To_TOWN: {
                    return 'Partagée Interville';
                }
                case Transfer::PORTEAPORTE_TRANSFERT_To_TOWN: {
                    return 'Partagée Aéroport-Domicile';
                }
                case Transfer::GARE_TRANSFER: {
                    return 'Aéroport-Gare';
                }
                case Transfer::PARIS_TRANSFER: {
                    return 'Aéroport-Région Parisienne';
                }
                case Transfer::PRIVATE_TRANSFER_TO_AIRPORT: {
                    return 'Privé Aéroport-Aéroport';
                }
                case Transfer::PRIVATE_TRANSFER_TO_TOWN: {
                    return 'Privé Aéroport-Domicile';
                }
                case Transfer::PARTICULAR_COMMAND:{
                    return 'Commande Particulière';
                }
                case Transfer::PARIS_AIRPORT:{
                    return 'Paris Aéroport';
                }
                default:
                    break;
            }
        }, $data));
        return array('prestation' => $result ,'total' => sizeof($result) ,'data' => $data);

    }

    public function getStatsByPickupOrDropOfOrdersNumber($startdate, $endate ,$sens)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getOrdersNumberPerPickUpOrDrop($day1, $day2, $sens);
        $drop=[];
        $pickup=[];

/*        $pickup['Vatry'] = 0;
        $drop['Vatry'] = 0;*/

        foreach ($data as $dt) {
            $name =str_replace("'"," ",$dt['name']);

            if ($dt['direction'] == Transfer::FROM_VATRY) {
                if (!isset($drop[$name])) {
                    $drop[$name] = 1;
                } else {
                    $drop[$name]++;
                }
               // $pickup['Vatry']++;
            } else {
                if (!isset($pickup[$name])) {
                    $pickup[$name] = 1;
                } else {
                    $pickup[$name]++;
                }
               // $drop['Vatry']++;
            }

        }
        return array('pickup' => $pickup, 'drop' => $drop ,'total' => sizeof($data) , 'data' => $data);
    }

    public function getStatsByPassengersNumberOfOrdersNumber($startdate, $endate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($endate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getOrdersNumberPerPassengers($day1, $day2);
        $result = array_count_values(array_map(function ($value) {
            return ($value['qty'] + $value['qtyChild'] + $value['qtyBaby']).' Pax';
        }, $data));
        return array('p1c' => $result,'total' => sizeof($result),'data' => $data);
    }

    public function getStatsOfRevenuesBySource($startdate, $enddate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($enddate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getPaidTransfers($day1, $day2);
        $data=array_map(function ($value) {
            if($value)
                return [
                    'id' => $value->getId(),
                    'date' => $value->getPickupDate(),
                    'type' => ($value->getAffectedTo()&&$value->getCreatedBy()!=$value->getAffectedTo())?StatisticsService::getSourceName($value->getAffectedTo()->getType()):StatisticsService::getSourceName($value->getCreatedBy()->getType()),
                    'partner' => $value->getPartner()?$value->getPartner()->getName():null,
                    'price' => (($value->getAffectedTo()&&$value->getAffectedTo()->getAgencePartner())||$value->getCreatedBy()->getAgencePartner())?($value->getPrice()*(1-($value->getAffectedTo()->getAgencePartner()->getCommission()/100))):$value->getPrice()
                ];
        }, $data);
        $result = array();

        foreach ($data as $dr) {
            $partner =str_replace("'"," ",$dr['partner']);

            if($partner) {

                if (!isset($result[$partner])) {
                    $result[$partner] = $dr['price'];
                } else {
                    $result[$partner] += $dr['price'];
                }
            }
            else
            {
                if (!isset($result[$dr['type']])) {
                    $result[$dr['type']] = $dr['price'];
                } else {
                    $result[$dr['type']] += $dr['price'];
                }
            }

        }
        return array('source' => $result,'total' => sizeof($result),'data' => $data);

    }

    public function getStatsOfRevenuesByPrestationType($startdate, $enddate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($enddate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getRevenuesByPrestationType($day1, $day2);
        $result = array();
        $total=0;
        foreach ($data as &$dr) {

            if(($dr['transfert']->getAffectedTo()&& $dr['transfert']->getAffectedTo()->getAgencePartner())||$dr['transfert']->getCreatedBy()->getAgencePartner()){

                $dr['price']= $dr['transfert']->getPrice()*(1-($dr['transfert']->getAffectedTo()->getAgencePartner()->getCommission()/100));

            }


            switch ($dr['type']) {
                case Transfer::INTERVILLE_TRANSFERT_To_TOWN: {
                    if (!isset($result['Partagée Interville'])) {
                        $result['Partagée Interville']=$dr['price'];
                        break;
                    } else {
                        $result['Partagée Interville'] +=$dr['price'];
                        break;
                    }

                }
                case Transfer::PORTEAPORTE_TRANSFERT_To_TOWN: {
                    if (!isset($result['Partagée Aéroport-Domicile'])) {
                        $result['Partagée Aéroport-Domicile'] =$dr['price'];
                        break;
                    } else {
                        $result['Partagée Aéroport-Domicile'] +=$dr['price'];
                        break;
                    }
                }
                case Transfer::GARE_TRANSFER: {
                    if (!isset($result['Aéroport-Gare'])) {
                        $result['Aéroport-Gare'] =$dr['price'];
                        break;
                    } else {
                        $result['Aéroport-Gare'] +=$dr['price'];
                        break;
                    }
                }
                case Transfer::PARIS_TRANSFER: {
                    if (!isset($result['Aéroport-Région Parisienne'])) {
                        $result['Aéroport-Région Parisienne'] =$dr['price'];
                        break;
                    } else {
                        $result['Aéroport-Région Parisienne'] +=$dr['price'];
                        break;
                    }
                }
                case Transfer::PRIVATE_TRANSFER_TO_AIRPORT: {
                    if (!isset($result['Privé Aéroport-Aéroport'])) {
                        $result['Privé Aéroport-Aéroport'] =$dr['price'];
                        break;
                    } else {
                        $result['Privé Aéroport-Aéroport'] +=$dr['price'];
                        break;
                    }
                }
                case Transfer::PRIVATE_TRANSFER_TO_TOWN: {
                    if (!isset($result['Privé Aéroport-Domicile'])) {
                        $result['Privé Aéroport-Domicile'] =$dr['price'];
                        break;
                    } else {
                        $result['Privé Aéroport-Domicile'] +=$dr['price'];
                        break;
                    }
                }

                case Transfer::PARTICULAR_COMMAND: {
                    if (!isset($result['Commande Particulière'])) {
                        $result['Commande Particulière'] =$dr['price'];
                        break;
                    } else {
                        $result['Commande Particulière'] +=$dr['price'];
                        break;
                    }
                }

                case Transfer::PARIS_AIRPORT: {
                    if (!isset($result['Paris Aéroport'])) {
                        $result['Paris Aéroport'] =$dr['price'];
                        break;
                    } else {
                        $result['Paris Aéroport'] +=$dr['price'];
                        break;
                    }
                }

                default:
                    break;
            }

        }
        return array('prestation' => $result,'total' => sizeof($result),'data' => $data);
    }

    public function getStatsOfRevenuesByPickupOrDrop($startdate, $enddate,$sens)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($enddate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getRevenuesByPickupOrDrop($day1, $day2, $sens);

     //   $pickup['Vatry'] = 0;
      //  $drop['Vatry'] = 0;
        $drop=[];
        $pickup=[];
        foreach ($data as &$dt) {
            $name =str_replace("'"," ",$dt['name']);

            if(($dt['transfert']->getAffectedTo()&& $dt['transfert']->getAffectedTo()->getAgencePartner())||$dt['transfert']->getCreatedBy()->getAgencePartner()){

                $dt['price']= $dt['transfert']->getPrice()*(1-($dt['transfert']->getAffectedTo()->getAgencePartner()->getCommission()/100));

            }

            if ($dt['direction'] == Transfer::FROM_VATRY) {

                if (!isset($drop[$name])) {
                    $drop[$name] = $dt['price'];
                } else {
                    $drop[$name] += $dt['price'];
                }
      //          $pickup['Vatry'] += $dt['price'];
            } else {

                if (!isset($pickup[$name])) {
                    $pickup[$name] = $dt['price'];
                } else {
                    $pickup[$name] += $dt['price'];
                }
 //               var_dump($pickup);die('else');
        //        $drop['Vatry'] += $dt['price'];
            }

        }

        if ($sens==Transfer::FROM_VATRY)
            return array('pickup' => $pickup,'drop' => $drop ,'total' => sizeof($drop),'data' => $data);
        else
            return array('pickup' => $pickup,'drop' => $drop ,'total' => sizeof($pickup),'data' => $data);
    }

    public function getStatsOfRevenuesByPaymentType($startdate, $enddate)
    {
        $day1 = new \DateTime($startdate);
        $day2 = new \DateTime($enddate);
        $data = $this->em->getRepository('AppBundle:Transfer')->getRevenuesByPaymentType($day1, $day2);
        $result = array();
        $data=array_map(function ($value) {
            if($value)
                return [
                    'id' => $value->getId(),
                    'date' => $value->getPickupDate(),
                    'type' => $value->getPayment()?$value->getPayment()->getType():'vad',
                    'price' => (($value->getAffectedTo()&&$value->getAffectedTo()->getAgencePartner())||$value->getCreatedBy()->getAgencePartner())?($value->getPrice()*(1-($value->getAffectedTo()->getAgencePartner()->getCommission()/100))):$value->getPrice()
                ];
        }, $data);
//        var_dump($data);die;
        foreach ($data as $dr) {
            switch($dr['type']) {
                case Transfer::TYPE_B2B: {
                    if (!isset($result['B2B'])) {
                        $result['B2B'] = $dr['price'];
                        break;
                    } else {
                        $result['B2B'] += $dr['price'];
                        break;
                    }
                }
                case Transfer::TYPE_CACHE: {
                    if (!isset($result['Cash'])) {
                        $result['Cash'] = $dr['price'];
                        break;
                    } else {
                        $result['Cash'] += $dr['price'];
                        break;
                    }
                }
                case Transfer::TYPE_CREDIT_CARD: {
                    if (!isset($result['Carte de crédit'])) {
                        $result['Carte de crédit'] = $dr['price'];
                        break;
                    } else {
                        $result['Carte de crédit'] += $dr['price'];
                        break;
                    }
                }
                case 'vad': {
                    if (!isset($result['vad'])) {
                        $result['vad'] = $dr['price'];
                        break;
                    } else {
                        $result['vad'] += $dr['price'];
                        break;
                    }
                }

                case Transfer::TYPE_CHEQUE: {
                    if (!isset($result['chèque'])) {
                        $result['chèque'] = $dr['price'];
                        break;
                    } else {
                        $result['chèque'] += $dr['price'];
                        break;
                    }
                }

                case Transfer::TYPE_VIREMENT: {
                    if (!isset($result['Virement'])) {
                        $result['Virement'] = $dr['price'];
                        break;
                    } else {
                        $result['Virement'] += $dr['price'];
                        break;
                    }
                }
            }
        };
        return array('paiement' => $result,'total' => sizeof($result),'data' => $data);

    }

    public function getStatsOfMain()
    {   $startmonth=new \DateTime('first day of this month 00:00:00');
        $endmonth=new \DateTime('last day of this month 00:00:00');
        $totalmonth=0;
        $data = $this->em->getRepository('AppBundle:Transfer')->getTransfersByPeriod($startmonth->format('Y-m-d H:i:s'),$endmonth->format('Y-m-d H:i:s'));
        $result=array_count_values(array_map(function($value) use (&$totalmonth) {$totalmonth+=$value['price'];return $value['createdAt']->format('Y-m-d'); },$data));
        $startyear=new \DateTime('first day of January ');
        $endyear=new \DateTime('last day of December ');
        $endyear->setTime(23,59,59);
        $totalyear=0;
        $data2 = $this->em->getRepository('AppBundle:Transfer')->getTransfersByPeriod($startyear->format('Y-m-d H:i:s'),$endyear->format('Y-m-d H:i:s'));
        $result2=array_count_values(array_map(function($value) use (&$totalyear) {$totalyear+=$value['price'];return $value['createdAt']->format('Y-m-d'); },$data2));
        return array('data' => $data , 'months' => $result , 'totalmonth' => $totalmonth.' €' ,'totalMonthTransfers' => array_sum($result),
                     'dataYear' => $data2,'year' => $result2,'totalyear' => $totalyear.' €' ,'totalYearTransfers' => array_sum($result2)
        );
    }
}