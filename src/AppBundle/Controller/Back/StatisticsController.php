<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 22/06/2016
 * Time: 14:59
 */

namespace AppBundle\Controller\Back;

use AppBundle\Entity\Transfer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Debug\Exception\ContextErrorException;

/**
 * @Route("/stats")
 */
class StatisticsController extends Controller
{

    /**
     * @Route("/",name="stat_main")
     */
    public function index()
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }

        $parent = $this->get('common.back.service')->getMenuExtends();
        $data = $this->get('statistics.service')->getStatsOfMain();
        arsort($data['months']);
        arsort($data['year']);
        $bestmonth = reset($data['months']) . ' (' . key($data['months']) . ')';
        $bestyear = reset($data['year']) . ' (' . key($data['year']) . ')';
        return $this->render('@App/Back/Statistics/main.html.twig', array(
            'monthsStat' => json_encode($data['months']),
            'bestTransfersMonth' => $bestmonth,
            'totalmonth' => $data['totalmonth'],
            'totalMonthTransfers' => $data['totalMonthTransfers'],
            'yearStat' => json_encode($data['year']),
            'bestTransfersYear' => $bestyear,
            'totalyear' => $data['totalyear'],
            'totalYearTransfers' => $data['totalYearTransfers'],
            'parent' => $parent
        ));

    }

    /**
     * @Route("/ndp/{type}",name="ndp")
     */
    function passengersNumberPerType(Request $request, $type = null)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        $parent = $this->get('common.back.service')->getMenuExtends();
        $startdate = \DateTime::createFromFormat('d-m-Y', $request->get('start'));
        $enddate = \DateTime::createFromFormat('d-m-Y', $request->get('end'));

        if ($startdate > $enddate || !$startdate || !$enddate) {
            $now = new \DateTime('today');
            $startdate = $now;
            $enddate = $now;
        }
        $startdate->setTime(0, 0, 0);
        $enddate->setTime(23, 59, 59);

        switch ($type) {
            case null: {
                return $this->redirect($this->generateUrl('stat_main'));
            }
            case 'pickup' : {
                $sens = Transfer::TO_VATRY;
                $sens2 = Transfer::FROM_VATRY;
                $data = $this->get('statistics.service')->getStatsByPickUpOrDropLocationOfPassengerNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'), $sens,$sens2);
                arsort($data['pickup']);
//                var_dump(json_encode($data['pickup']));die();
                return $this->render('@App/Back/Statistics/ndp_pickupordrop.html.twig', array(
                    'data' => json_encode($data['pickup']),
                    'parent' => $parent,
                    'total' => $data['total'],
                    'transfers' => $data['data'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'requestedPage' => 'vers l aéroport Paris-Vatry par lieu de Pickup',
                    'unit' => 'PAX',
                    'isMoney' => false
                ));
            }
            case 'drop' : {
                $sens = Transfer::FROM_VATRY;
                $sens2 = Transfer::TO_VATRY;
                $data = $this->get('statistics.service')->getStatsByPickUpOrDropLocationOfPassengerNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'), $sens,$sens2);
                arsort($data['drop']);
                return $this->render('@App/Back/Statistics/ndp_pickupordrop.html.twig', array(
                    'data' => json_encode($data['drop']),
                    'total' => $data['total'],
                    'transfers' => $data['data'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'requestedPage' => 'de l aéroport Paris-Vatry par lieu de Drop',
                    'unit' => 'PAX',
                    'parent' => $parent,
                    'isMoney' => false
                ));
            }
            case 'prestation': {
                $data = $this->get('statistics.service')->getStatsByPrestationTypeOfPassengerNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/ndp_prestationType.html.twig', array(
                    'data' => json_encode($data['prestations']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => 'PAX',
                    'parent' => $parent,
                    'isMoney' => false
                ));
            }
            case 'senstransfer': {
                $data = $this->get('statistics.service')->getStatsByTransferWayOfPassengerNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/ndp_sensTransfer.html.twig', array(
                    'data' => json_encode($data['transferway']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => 'PAX',
                    'parent' => $parent,
                    'isMoney' => false
                ));
            }
            case 'destinationvols': {
                $data = $this->get('statistics.service')->getStatsByFlightDestinationOfPassengerNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/ndp_flightDestination.html.twig', array(
                    'data' => json_encode($data['destinationslvol']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => 'PAX',
                    'parent' => $parent,
                    'isMoney' => false
                ));

            }
            case 'originvols': {
                $data = $this->get('statistics.service')->getStatsByFlightOriginOfPassengerNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/ndp_flightOrigin.html.twig', array(
                    'data' => json_encode($data['originvol']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => 'PAX',
                    'parent' => $parent,
                    'isMoney' => false
                ));

            }
            default:
                return $this->redirect($this->generateUrl('stat_main'));
        }

    }

    /**
     * @Route("/ndc/{type}",name="ndc")
     */
    function ordersNumberPerType(Request $request, $type = null)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        $parent = $this->get('common.back.service')->getMenuExtends();
        $startdate = \DateTime::createFromFormat('d-m-Y', $request->get('start'));
        $enddate = \DateTime::createFromFormat('d-m-Y', $request->get('end'));

        if ($startdate > $enddate || !$startdate || !$enddate) {
            $now = new \DateTime('today');
            $startdate = $now;
            $enddate = $now;
        }

        $startdate->setTime(0, 0, 0);
        $enddate->setTime(23, 59, 59);
        switch ($type) {
            case null: {
                return $this->redirect($this->generateUrl('stat_main'));
            }
            case 'source': {

                $data = $this->get('statistics.service')->getStatsByOrdersSource($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/ndc_source.html.twig', array(
                    'data' => json_encode($data['ordersource']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => 'Commandes',
                    'parent' => $parent,
                    'isMoney' => false
                ));

            }
            case 'prestation': {
                $data = $this->get('statistics.service')->getStatsByPrestationTypeOfOrdersNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/ndc_prestationType.html.twig', array(
                    'data' => json_encode($data['prestation']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => 'Commandes',
                    'parent' => $parent,
                    'isMoney' => false
                ));
            }
            case 'pickup': {
                $sens = Transfer::TO_VATRY;
                $data = $this->get('statistics.service')->getStatsByPickupOrDropOfOrdersNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'), $sens);
                return $this->render('@App/Back/Statistics/ndc_pickupordrop.html.twig', array(
                    'data' => json_encode($data['pickup']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'requestedPage' => 'vers l aéroport Paris-Vatry par lieu de Pickup ',
                    'unit' => 'Commandes',
                    'parent' => $parent,
                    'isMoney' => false
                ));
            }
            case 'drop': {
                $sens = Transfer::FROM_VATRY;
                $data = $this->get('statistics.service')->getStatsByPickupOrDropOfOrdersNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'), $sens);
                return $this->render('@App/Back/Statistics/ndc_pickupordrop.html.twig', array(
                    'data' => json_encode($data['drop']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'requestedPage' => 'de l aéroport Paris-Vatry par lieu de Drop',
                    'unit' => 'Commandes',
                    'parent' => $parent,
                    'isMoney' => false
                ));
            }
            case 'p1c': {
                $data = $this->get('statistics.service')->getStatsByPassengersNumberOfOrdersNumber($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/ndc_p1c.html.twig', array(
                    'data' => json_encode($data['p1c']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => 'Commandes',
                    'parent' => $parent,
                    'isMoney' => false
                ));

            }
            default:
                return $this->redirect($this->generateUrl('stat_main'));
        }
    }

    /**
     * @Route("/cac/{type}",name="cac")
     */
    public function revenuesPerType(Request $request, $type = null)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        $parent = $this->get('common.back.service')->getMenuExtends();
        $startdate = \DateTime::createFromFormat('d-m-Y', $request->get('start'));
        $enddate = \DateTime::createFromFormat('d-m-Y', $request->get('end'));

        if ($startdate > $enddate || !$startdate || !$enddate) {
            $now = new \DateTime('today');
            $startdate = $now;
            $enddate = $now;
        }

        $startdate->setTime(0, 0, 0);
        $enddate->setTime(23, 59, 59);

        switch ($type) {
            case null: {
                return $this->redirect($this->generateUrl('stat_main'));
            }
            case 'source': {
                $data = $this->get('statistics.service')->getStatsOfRevenuesBySource($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/cac_source.html.twig', array(
                    'data' => json_encode($data['source']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => '€',
                    'parent' => $parent,
                    'isMoney' => true
                ));
            }
            case 'prestation': {
                $data = $this->get('statistics.service')->getStatsOfRevenuesByPrestationType($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/cac_prestationType.html.twig', array(
                    'data' => json_encode($data['prestation']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => '€',
                    'parent' => $parent,
                    'isMoney' => true
                ));
            }
            case 'pickup': {
                $sens = Transfer::TO_VATRY;
                $data = $this->get('statistics.service')->getStatsOfRevenuesByPickupOrDrop($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'), $sens);
                return $this->render('@App/Back/Statistics/cac_pickupordrop.html.twig', array(
                    'data' => json_encode($data['pickup']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'requestedPage' => 'vers l aéroport Paris-Vatry par lieu de Pickup',
                    'unit' => '€',
                    'parent' => $parent,
                    'isMoney' => true
                ));
            }
            case 'drop': {
                $sens = Transfer::FROM_VATRY;
                $data = $this->get('statistics.service')->getStatsOfRevenuesByPickupOrDrop($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'), $sens);
                return $this->render('@App/Back/Statistics/cac_pickupordrop.html.twig', array(
                    'data' => json_encode($data['drop']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'requestedPage' => 'de l aéroport Paris-Vatry par lieu de Drop',
                    'unit' => '€',
                    'parent' => $parent,
                    'isMoney' => true
                ));
            }
            case 'paiement': {
                $data = $this->get('statistics.service')->getStatsOfRevenuesByPaymentType($startdate->format('Y-m-d H:i:s'), $enddate->format('Y-m-d H:i:s'));
                return $this->render('@App/Back/Statistics/cac_paymentType.html.twig', array(
                    'data' => json_encode($data['paiement']),
                    'total' => $data['total'],
                    'startdate' => $startdate->format('d-m-Y'),
                    'enddate' => $enddate->format('d-m-Y'),
                    'transfers' => $data['data'],
                    'unit' => '€',
                    'parent' => $parent,
                    'isMoney' => true
                ));
            }
            default:
                return $this->redirect($this->generateUrl('stat_main'));
        }
    }

    private function __has_access()
    {
        $allowed_roles = ['ROLE_ADMIN', 'ROLE_PARTNER', 'ROLE_ASSOCIATE'];
        $user = $this->getUser();
        if ($user != null) {
            if (!in_array($user->getRoles()[0], $allowed_roles)) {
                return false;
            } elseif (in_array('ROLE_PARTNER', $user->getRoles())) {
                if (!$user->getPartner()->getIsAirport()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

}