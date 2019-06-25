<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Transfer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/Common")
 */
class CommonController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/vente/{type}",name="reservationB2")
     */
    public function venteBackAction($type=null){

        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
            {
                $parent="@App/Back/index.html.twig";
            }
        elseif(in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles()))
            {
          $parent= '@App/PartnerAgency/index.html.twig';
           }
        else
        {
           $parent =  '@App/AgentAdmin/index.html.twig' ;
    }
        if($type=='B2B'||$type=='B2C')
        {
            return $this->render("@App/Back/Transfers/achat_b2.html.twig",array('parent'=>$parent,'type' => $type));
        }
        else
        {
            return $this->redirect('/');
        }
    }

    public function getMenu(){
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles()))
        {
            $parent="@App/Back/index.html.twig";
        }
        elseif(in_array('ROLE_PARTNER_AGENCY', $this->getUser()->getRoles()))
        {
            $parent= '@App/PartnerAgency/index.html.twig';
        }
        else
        {
            $parent =  '@App/AgentAdmin/index.html.twig' ;
        }
    }

}
