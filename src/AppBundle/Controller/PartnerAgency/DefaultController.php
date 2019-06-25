<?php

namespace AppBundle\Controller\PartnerAgency;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DefaultController
 * @package AppBundle\Controller\PartnerAgency
 * @Route("/part_agency")
 */
class DefaultController extends Controller {

    /**
     * @Route("/",name="home_partner_agency")
     */
    public function homeAction(){
//        return $this->redirectToRoute("partner_agency_account");
        return $this->redirectToRoute('agency_all_transfers', array(
            'type' => "all",
            'affected' => true
        ));
    }

}