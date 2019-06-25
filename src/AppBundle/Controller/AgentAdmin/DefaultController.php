<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\AgentAdmin;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/agent-admin")
 */
class DefaultController extends Controller {

    /**
     * @Route("/",name="home_agent_admin")
     */
    public function homeAction(){
        return $this->redirectToRoute("agents_list");
    }

}