<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 22/05/2016
 * Time: 19:19
 */

namespace AppBundle\Controller\Front;


use AppBundle\Service\Front\Flights\FlightService;
use AppBundle\Service\Front\Flights\ServiceFlight;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FlightsController extends Controller {


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/flight_list",name="flight_list",options={"expose"=true})
     */
    public function listAction(Request $request){

        if($request->query->has('date')){
            $date = \DateTime::createFromFormat('d/m/Y',$request->query->get('date'));
        }else{
            $date = new \DateTime('today');
        }

        if($request->query->has('dir') && $request->query->get('dir') == 'from'){
            $dir = 'from';
        }else{
            $dir = 'to';
        }

        $nextDate = Helpers::createDateTimeByMinutes($date,1440);

        $qb = $this->getDoctrine()->getRepository("AppBundle:Flight")->createQueryBuilder('f')
            ->where('f.time >= :date')
            ->andWhere("f.time < :nextDate")
            ->setParameter('date',$date->format('Y-m-d'))
            ->setParameter('nextDate',$nextDate->format('Y-m-d'));

        if ($dir =='to'){
            $qb->andWhere("f.toLocation = 'vatry' ");
        }else{
            $qb->andWhere("f.fromLocation = 'vatry' ");
        }

       $list= $qb ->getQuery()
            ->getResult();

        if (!$request->isXmlHttpRequest()){
            return $this->render("@App/front/Flights/list.html.twig",array(
                'list' => $list,
                'dir' => 'to'
            ));
        }else{
            return new JsonResponse(array(
                'html' => $this->renderView("@App/front/Flights/list.html.twig",array(
                    'list' => $list,
                    'dir' => $dir
                ))
            ));
        }


    }


    /**
     * @Route("/getFlight")
     */
    public function getFlight(Request $req){

        $flightservice = new FlightService();
        $flight=$flightservice->flightStatus($req->get('volnum'));
        $flightDB = $this->getDoctrine()->getRepository("AppBundle:Flight")->getFlight($req->get('volnum'));
        $details=$flightservice->getFlightDetails($flightDB[0],$req->get('date'));
        $track=null;
        if($details['status']=='En vol')
        {
            $track=$flightservice->getTrackLog($req->get('volnum'));
        }
        $response = new JsonResponse();
        $response->setData(array(
            'longitude' => $flight['longitude'],
            'latitude' => $flight['latitude'],
            'from' => $details['from'],
            'to' => $details['to'],
            'departprogrammee' => $details['departprogrammee'],
            'arriveestimee' => $details['arriveestimee'],
            'status' => $details['status'],
            'trackhistory' => $track,
        ));
        return $response;

    }

}