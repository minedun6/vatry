<?php

namespace AppBundle\Controller\Secretary;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/secretary")
 */
class DefaultController extends Controller {

    /**
     * @Route("/",name="home_secretary")
     */
    public function homeAction(){
        return $this->redirectToRoute("agents_list");
    }

}