<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\Agent;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Agent
 * @Route("/agent")
 */
class DefaultController extends Controller
{

    /**
     * @Route("/",name="home_agent")
     */
    public function homeAction()
    {

//        return $this->redirectToRoute("agent_all_transfers",array('type' => "all") );
        return $this->redirectToRoute("agent_all_transfers");
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ma-balance", name="agent_balance")
     */
    public function myBalanceAction(Request $request)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $user = $this->getUser();
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getRepository('AppBundle:Balance');
            $start_date = $request->get('start_date') != null ? new \DateTime($request->get('start_date')) : null;
            $end_date = $request->get('end_date') != null ? new \DateTime($request->get('end_date')) : null;
            if ($start_date != null && $end_date != null) {
                $balances = $em->findBalancesByPeriodForAgent($start_date, $end_date, $user->getId());
                return $this->render('@App/Agent/Balance/balance.html.twig', array(
                    'balances' => $balances,
                    'date' => $start_date,
                    'end_date' => $end_date,
                    'parent' => $parent
                ));
            } elseif ($start_date != null) {
                $balances = $em->findBalancesByDateForAgent($start_date, $user->getId());
                return $this->render('@App/Agent/Balance/balance.html.twig', array(
                    'balances' => $balances,
                    'date' => $start_date,
                    'parent' => $parent
                ));
            }
        }
        $date = new \DateTime('now');
        $em = $this->getDoctrine()->getManager();
        $balances = $em->getRepository("AppBundle:Balance")->findBalancesByDateForAgent($date, $user->getId());
        
        return $this->render('@App/Agent/Balance/balance.html.twig', array(
            'balances' => $balances,
            'date' => $date,
            'parent' => $parent
        ));
    }

}