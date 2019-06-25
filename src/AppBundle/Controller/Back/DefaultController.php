<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\Back;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Back
 * @Route("/admin")
 */
class DefaultController extends Controller {

    /**
     * @Route("/",name="back_index")
     */
    public function homeAction(){

        return $this->redirectToRoute("admin_all_transfers");
    }

}