<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Transfer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('::base.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/private_transfer/0",name="private_transfer_0")
     */
    public function privateTransferIndex(){

        return $this->render("@App/front/private_transfer_step_0.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/conditions_d_utilisation",name="conditions")
     */
    public function cgvAction(){

        return $this->render("@App/conditions.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/conditions_d_utilisation_b2b",name="conditions_b2b")
     */
    public function cgvB2BAction(){

        return $this->render("@App/conditions_b2b.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ok")
     */
    public function okAction(){
        return $this->render("@App/front/ok.html.twig");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/nok")
     */
    public function nokAction(){
        return $this->render("@App/front/nok.html.twig");
    }




}
