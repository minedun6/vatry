<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\Agent;


use AppBundle\Entity\Transfer;
use AppBundle\Entity\Agent;
use AppBundle\Entity\User;
use AppBundle\Entity\Person;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
/**
 * Class DefaultController
 * @package AppBundle\Controller\Back
 * @Route("/encaissement")
 */
class EncaissementController extends Controller {

    /**
     * @Route("/agnet_encaissement",name="encaiss")
     */


    public function Encaissement(Request $request){

        $dateTime=new \DateTime("now");
        $dateTime=$dateTime->format('d/m/Y');
        $dateTab = explode('/',$dateTime);

        $date = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];

        $encaissement=$this->getDoctrine()->getRepository("AppBundle:transfer")->
        encaissement_jours($date,$this->getUser());




        $totalEncaissement = 0;
        $totalVAD=0;
        $totalCB=0;
        $totalEsp=0;




        foreach ($encaissement as $ep )

        { $op=$ep->getPaymentType();

          switch($op){
              case 'Espece':
              {
                  $totalEsp+=$ep->getPrice();
                  break;
              }
              case 'Carte bancaire':{
                  $totalCB+=$ep->getPrice();
                  break;
              }
              case 'VAD':
              {
                  $totalVAD+=$ep->getPrice();
                  break;
              }
              Default:break;
          }

        }
    $totalEncaissement=$totalEsp+$totalCB+$totalVAD;

        return $this->render("@App/Agent/Transfers/encaissements.html.twig", array(
            'encaissement' => $encaissement,
            'total'=>$totalEncaissement,
                'totalcb'=>$totalCB,
                'totalvad'=>$totalVAD,
                'totalesp'=> $totalEsp
            )

        );


    }



    /**
     * @param User $agent
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/agent_transfer/list_between_dates",name="list_between_dates")
     */

    public function getListBetweenDates(Request $request)

    {

        $day2 = new \DateTime($request->get('day1'));
        $day1 = new \DateTime($request->get('day2'));

        $encaissements=null;
        if($day1!=$day2 ) {
            $day2->add(new \DateInterval('P1D'));
            $encaissements = $this->getDoctrine()->getRepository("AppBundle:Transfer")->periode_encaissement_agent($day1, $day2, $this->getUser());

        }else{
            $day1=$day1->format('d/m/Y');
            $dateTab = explode('/',$day1);

            $date = $dateTab[2]."-".$dateTab[1]."-".$dateTab[0];

            $encaissements=$this->getDoctrine()->getRepository("AppBundle:transfer")->
            encaissement_jours($date,$this->getUser());

        }


        $data = array();

        for($i=0;$i<sizeof($encaissements);$i++)
       {    array_push($data,['createdAt' => $encaissements[$i]->getCreatedAt()->format('d/m/Y'),
           'price' => $encaissements[$i]->getPrice(),
           'type' => $encaissements[$i]->getPaymentType()]);


       }
       return new JsonResponse($data);
    }


}