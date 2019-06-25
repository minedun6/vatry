<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 14/04/2016
 * Time: 21:17
 */

namespace AppBundle\Controller\Back;


use AppBundle\Entity\Flight;
use AppBundle\Entity\User;
use AppBundle\Entity\Transfer;
use AppBundle\Utilities\Helpers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\ModifType;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Transfer
 * @package AppBundle\Controller\Back
 * @Route("/agent/gestion_navette")
 */
class NavetteController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/details_navette/{flight}/{date}/{type}",name="navette_detail")
     */
    public function TestTransfertNavette(Request $request, Flight $flight, $date, $type = null)
    {
        if ($type == "paris") {
            $presta = Transfer::PARIS_TRANSFER;
        } else {
            $presta = Transfer::GARE_TRANSFER;
        }
        $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->getTransfertByFlightAndDateAndType($flight, $date, $presta);

        return $this->render("@App/Back/Navette/details_navette.html.twig", array(
            'transfers' => $transfers,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/transfer_navette/{type}",name="transfer_navette")
     */
    public function TransfertNavette(Request $request, $type = null)
    {

        if ($request->getMethod() == "POST") {
            $dateTime = $request->request->get('date');

        } else {
            $dateTime = new \DateTime("now");
            $dateTime = $dateTime->format('Y/m/d');
        }

        $date = Helpers::convertDateTimeToDbDate($dateTime);
        $navettes = [];
        $navette = [];

        if (!$type) {
            $typeTransfert = Transfer::GARE_TRANSFER;
        } elseif ($type == 'prive') {
            $typeTransfert = Transfer::PRIVATE_TRANSFER_TO_TOWN;
        } else {
            $typeTransfert = Transfer::PARIS_TRANSFER;
        }
        // on séléctionne les vols relatifs à cette date
        $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
            ->getFlightsByDate($date);

        // pour chaque vol on extrait les transferts qui les sont associés puis on les traite
        foreach ($flights as $flight) {
            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->getTransfertByFlightAndDateAndType($flight, $date, $typeTransfert);
            if ($transfers) {
                if (!$type) {
                    $navette = $this->getNavetteGareInfos($transfers, $flight, $date);
                } else {
                    $navette = $this->getNavetteRPInfos($transfers, $flight, $date);
                }
                $navettes[] = $navette;
            }
        }

        return $this->render("@App/Back/Navette/navette.html.twig", array(
            'navettesGare' => $navettes,
            'type' => $type
        ));
    }

    /**
     * @param $transfers
     * @param $flight
     * @param $date
     * @return array
     */
    private function getNavetteGareInfos($transfers, $flight, $date)
    {
        $datePresta = $date;
        $presta = Transfer::GARE_TRANSFER;
        $Pickup = [];
        $Drop = [];

        if ($flight->getFromLocation() != "vatry") {
            $Pickup[] = "Aéroport Paris Vatry";
            $Drop[] = "Chalon-en-champagne";
            $Drop[] = "Reims";
        } else {
            $Pickup[] = "Reims";
            $Pickup[] = "Chalon-en-champagne";
            $Drop[] = "Aéroport Paris Vatry";
        }

        $totalAd = 0;
        $totalChild = 0;
        $totalBb = 0;

        foreach ($transfers as $transfer) {
            $totalAd += $transfer->getQty();
            $totalChild += $transfer->getQtyChild();
            $totalBb += $transfer->getQtyBaby();
        }

        $navette = array(
            'datePresta' => $datePresta,
            'presta' => $presta,
            'pickup' => $Pickup,
            'drop' => $Drop,
            'totalAd' => $totalAd,
            'totalBb' => $totalBb,
            'totalChild' => $totalChild,
            'vol' => $flight,
        );
        return ($navette);
    }


    /**
     * @param $transfers
     * @param $flight
     * @param $date
     * @return array
     */
    private function getNavetteRPInfos($transfers, $flight, $date)
    {
        $datePresta = $date;
        $presta = Transfer::PARIS_TRANSFER;
        $Pickup = [];
        $Drop = [];

        $totalAd = 0;
        $totalChild = 0;
        $totalBb = 0;

        foreach ($transfers as $transfer) {
            $totalAd += $transfer->getQty();
            $totalChild += $transfer->getQtyChild();
            $totalBb += $transfer->getQtyBaby();
        }

        if ($flight->getFromLocation() != "vatry") {
            $Pickup[] = "Aéroport Paris Vatry";
            foreach ($this->getDistinctLocations($transfers, $flight) as $d)
                $Drop[] = $d;
        } else {
            foreach ($this->getDistinctLocations($transfers, $flight) as $p)
                $Pickup[] = $p;
            $Drop[] = "Aéroport Paris Vatry";
        }

        $navette = array(
            'datePresta' => $datePresta,
            'presta' => $presta,
            'pickup' => $Pickup,
            'drop' => $Drop,
            'totalAd' => $totalAd,
            'totalBb' => $totalBb,
            'totalChild' => $totalChild,
            'vol' => $flight,
        );

        return ($navette);
    }

    private function getDistinctLocations($transfers, $flight)
    {
        $locations = [];
        foreach ($transfers as $transfer) {
            if ($transfer->getFlight() == $flight) {
                if (!in_array($transfer->getLocation(), $locations)) {
                    $locations[] = $transfer->getLocation();
                }
            } else {
                if (!in_array($transfer->getLocation2(), $locations)) {
                    $locations[] = $transfer->getLocation2();
                }
            }
        }
        $locationsName = [];
        if ($locations) {
            foreach ($locations as $lname) {
                $locationsName[] = $lname->getName();
            }
        }
        return $locationsName;

    }

    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->container->getParameter('kernel.logs_dir') . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }

    /**
     * @Route("/tests", name="navette_tests")
     */
    public function testAction()
    {
        $date = new \DateTime("now");
        return new Response("");

    }

}