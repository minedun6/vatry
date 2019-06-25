<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 16/04/2016
 * Time: 16:42
 */

namespace AppBundle\Controller\Front;


use AppBundle\Entity\Flight;
use AppBundle\Entity\Location;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TransferApiController
 * @package AppBundle\Controller\Front
 * @Route("/api")
 */
class TransferApiController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/zip_code/{type}",name="get_zip_codes",options={"expose"=true})
     */
    public function getZipCodesAction(Request $request, $type = null)
    {
        $like = ($request->query->has('like')) ? $request->query->get('like') : null;

        $result = [];
        switch ($type) {
            case 'private' :
                $result = $this->getDoctrine()->getManager()->getRepository("AppBundle:PrivateLocationPrice")
                    ->createQueryBuilder("l")
                    ->select("DISTINCT l.zipCode")
                    ->where("l.zipCode NOT LIKE '%A%'")
                    ->getQuery()->getScalarResult();
                break;
            case 'airport_private' :
                $result = $this->getDoctrine()->getManager()->getRepository("AppBundle:PrivateLocationPrice")
                    ->createQueryBuilder("l")
                    ->select("DISTINCT l.zipCode")
                    ->where("l.zipCode LIKE '%A%'")
                    ->getQuery()->getScalarResult();
                break;
            case 'inter_ville' :
                $result = $this->getDoctrine()
                    ->getManager()->getRepository("AppBundle:InterVillePrices")->getZipCodes($like);
                break;
            case 'porte_a_porte' :
                $result = $this->getDoctrine()
                    ->getManager()->getRepository("AppBundle:PorteAPortePrice")->getZipCodes($like);
                break;
            case 'paris' :
                $result = $this->getDoctrine()
                    ->getManager()->getRepository("AppBundle:PorteAPortePrice")
                    ->getZipCodeForParisTransfer($like);
                break;
            default :
                $result = $this->getDoctrine()
                    ->getManager()->getRepository("AppBundle:Location")->getZipCodes($like);
                break;
        }


        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/location",name="get_locations",options={"expose"=true})
     */
    public function getTownsAction(Request $request)
    {
        // $like = ($request->query->has('like')) ? $request->query->get('like') : null;
        $zipCode = ($request->query->has('zipCode')) ? $request->query->get('zipCode') : null;
        $privateLocations = $this->getDoctrine()
            ->getManager()->getRepository("AppBundle:PrivateLocationPrice")->getLocationByZipCodes($zipCode);

        $result = $this->serializeLocations($privateLocations);
        return new JsonResponse($result);
    }

    /**
     * @return JsonResponse
     * @Route("/interVillelocation",name="get_interville_locations",options={"expose"=true})
     */
    public function getTownsInterVilleAction()
    {
        $towns = $this->getDoctrine()->getRepository("AppBundle:InterVillePrices")->getAvailableTownList();
        $result = [];
        foreach ($towns as $t) {
            $result[] = array(
                'zipCode' => $t->getZipCode(),
                'rdv' => $t->getRdv(),
                'town' => $t->getLocation()->getName(),
                'idLocation' => $t->getLocation()->getId()
            );
        }
        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/porteaportelocation",name="get_porteaporte_locations",options={"expose"=true})
     */
    public function getTownsPorteAPorteAction(Request $request)
    {
        $zipCode = ($request->query->has('zipCode')) ? $request->query->get('zipCode') : null;
        $porteAportes = $this->getDoctrine()
            ->getManager()->getRepository("AppBundle:PorteAPortePrice")->getLocationByZipCodes($zipCode);
        $results = $this->serializeLocations($porteAportes);
        return new JsonResponse($results);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/paris_transfer_locations",name="get_paris_locations",options={"expose"=true})
     */
    public function getParisLocationAction(Request $request)
    {
        $zipCode = ($request->query->has('zipCode')) ? $request->query->get('zipCode') : null;
        $locations = $this->getDoctrine()
            ->getManager()->getRepository("AppBundle:PorteAPortePrice")
            ->getLocationsForParisTransfer($zipCode);
        $results = $this->serializeLocations($locations);
        return new JsonResponse($results);
    }

    /**
     * @return JsonResponse
     * @Route("/get_airports",name="get_airports",options={"expose"=true})
     */
    public function getAirportsAction()
    {
        $airports = $this->getDoctrine()
            ->getRepository("AppBundle:Location")->findBy(
                array(
                    'type' => Location::TYPE_AIRPORT
                )
            );

        return new JsonResponse(array(
            'data' => $this->serializeAirports($airports)
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/get_destination/{dest}",name="get_destinations",options={"expose"=true})
     */
    public function getDestinations(Request $request, $dest = null)
    {

        $type = $request->query->has('type') ? $request->query->get('type') : '';
        $date = $request->query->has('date') ? $request->query->get('date') : 'xx';
        $locationId = $request->query->has('location') ? $request->query->get('location') : 'xx';
        if ($date != 'xx') {
            $dateTab = explode('-', $date);
            if (count($dateTab) != 3) {
                $date = 'xx';
            } else {
                $date = $dateTab[2] . "-" . $dateTab[1] . "-" . $dateTab[0];
            }
        }
        //S'il y a un type donc un cas particulier
        if ($type != '') {
            if($type=="gare"||$type=="interville"||$type=="porteaporte"){
                // si c'est une gare on a déjà les city
                if($type=="gare") {
                    $locations = ['Porto', 'Oran', 'Marrakech', 'Nice'];
                    $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                        ->getDestinationForLocations($date, $type, $locations);
                }
                else if($type=="porteaporte"){
                        $locations= ['Porto', 'Nice','Marrakech'];
                        $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                            ->getDestinationForLocations($date, $type, $locations);
                }else{
                    // Cas de l'interville ou les cities sont dans la BD
                    $locations =[];
                    $em=$this->getDoctrine()->getManager();
                    $repL=$em->getRepository("AppBundle:Location");
                    $location=$repL->find($locationId);
                    if($location){
                        $repInterville=$em->getRepository("AppBundle:InterVillePrices");
                        $interville=$repInterville->findOneByLocation($location);
                        //Récupérer la liste des cités à gérer
                        $locations= $interville->getCitiesTable();
                    }
                    //Si on a pas de cité particulières c'est qu'il ne faut pas faire de cas particulier
                    if(empty($locations)){
                        $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                            ->getDestinationsByDate($date, $dest);
                    }else{
                        $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                            ->getDestinationForLocations($date, $type,$locations);
                    }
                }
            }else{
                $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                    ->getDestinationForParisAirpot($date, $type);
            }

            $result = $this->serializeTransfers($flights);

            return new JsonResponse($result);
        }
        $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
            ->getDestinationsByDate($date, $dest);

        $result = $this->serializeTransfers($flights);

        return new JsonResponse($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/get_provenances/{provenance}",name="get_provenances",options={"expose"=true})
     */
    public function getProvenances(Request $request, $provenance = null)
    {
        $type = $request->query->has('type') ? $request->query->get('type') : '';
        $date = $request->query->has('date') ? $request->query->get('date') : 'xx';
        $locationId = $request->query->has('location') ? $request->query->get('location') : 'xx';

        if ($date != 'xx') {
            $dateTab = explode('-', $date);
            if (count($dateTab) != 3) {
                $date = 'xx';
            } else {
                $date = $dateTab[2] . "-" . $dateTab[1] . "-" . $dateTab[0];
            }
        }
        if ($type != '') {
                // si c'est une gare on a déjà les city
            if($type=="gare"){
                $locations = ['Porto', 'Oran', 'Marrakech', 'Nice'];
                $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                    ->getProvenanceForLocations($date, $type, $locations);
            }else if ($type=="interville"){
                // Cas de l'interville ou les cities sont dans la BD
                $locations =[];
                $em=$this->getDoctrine()->getManager();
                $repL=$em->getRepository("AppBundle:Location");
                $location=$repL->find($locationId);
                if($location){
                    $repInterville=$em->getRepository("AppBundle:InterVillePrices");
                    $interville=$repInterville->findOneByLocation($location);
                    //Récupérer la liste des cités à gérer
                    $locations= $interville->getCitiesTable();
                }
                //Si on a pas de cité particulières c'est qu'il ne faut pas faire de cas particulier
                if(empty($locations)){
                    $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                        ->getProvenancesByDate($date, $provenance);
                }else{
                    $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")
                        ->getProvenanceForLocations($date, $type,$locations);
                }
            }
            $result = $this->serializeTransfers($flights);

            return new JsonResponse($result);
        }
        $flights = $this->getDoctrine()->getRepository("AppBundle:Flight")->getProvenancesByDate($date, $provenance);

        $result = $this->serializeTransfers($flights);

        return new JsonResponse($result);
    }

    /**
     * @param  Flight[] $flights
     * @return array
     */
    private function serializeTransfers($flights)
    {
        $result = [];
        foreach ($flights as $f) {
            $result[] = [
                'num' => $f->getNum(),
                'from' => $f->getFromLocation(),
                'to' => $f->getToLocation(),
                'time' => $f->getTime()->format('d/m/Y H:i'),
                'id' => $f->getId(),
                'pickUpTime' => $f->getTime()->format('H:i') //Todo to change
            ];
        }

        return $result;
    }

    /**
     * @param  Location $location
     * @return array
     */
    private function serializeLocation($location)
    {
        $result = [];
        $result[] = [
            'id' => $location->getid(),
            'name' => $location->getName(),
        ];
        return $result;
    }

    /**
     * @param   $datas
     * @return array
     */
    private function serializeLocations($datas)
    {
        $results = [];
        foreach ($datas as $data) {
            $results[] = [
                'id' => $data->getLocation()->getId(),
                'name' => $data->getlocation()->getName(),
            ];
        }
        return $results;
    }

    /**
     * @param Location[] $airports
     * @return array
     */
    private function serializeAirports($airports)
    {

        $result = [];
        foreach ($airports as $a) {
            $result[] = [
                'id' => $a->getId(),
                'name' => $a->getName()
            ];
        }

        return $result;

    }

}