<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\City;
use AppBundle\Form\Back\CityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class CityController extends Controller
{
    /**
     * @param Request $request
     * @Route("/new_city",name="new_city")
     */
    public function addAction(Request $request)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $city=new City();
        $form = $this->createForm(CityType::class, $city);

        if ($request->getMethod() == 'POST') {
            $city->activate();
            $form->handleRequest($request);
            $cities=null;
            if ($form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();
            }
            return $this->redirectToRoute('list_city');
        }
        return $this->render('@App/Back/City/add_edit.html.twig', array('form' => $form->createView(),
            'parent'=>$parent
        ));
    }

    /**
     * @param Request $request
     * @Route("/edit_city/{id}",name="edit_city")
     */
    public function editAction($id,Request $request)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em=$this->getDoctrine()->getManager();
        $repo=$em->getRepository("AppBundle:City");
        $city=$repo->find($id);
        $form = $this->createForm(CityType::class, $city);

        return $this->render('@App/Back/City/add_edit.html.twig', array('form' => $form,
            'parent'=>$parent
        ));
    }

    /**
     * @param Request $request
     * @Route("/list_city",name="list_city")
     */
    public function listAction()
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em=$this->getDoctrine()->getManager();

        $repo=$em->getRepository("AppBundle:City");
        $cities=$repo->findAll();

        return $this->render('@App/Back/City/list.html.twig',array('cities'=>$cities,
            'parent'=>$parent
        ));
    }
    /**
     * @param Request $request
     * @Route("/toogle_city/{id}",name="toogle_city")
     */
    public function toogleAction(City $city)
    {
        $em=$this->getDoctrine()->getManager();
        $em->persist($city);
        $city->toggle();
        $em->flush();
        return $this->redirectToRoute('list_city');
    }

    /**
     * @param Request $request
     * @Route("/testCT/{id}",name="tct")
     */
   /* public function testCTAction($id)
    {

        $em=$this->getDoctrine()->getManager();
        $repL=$em->getRepository("AppBundle:Location");
        //
        $location=$repL->find($id);
        $repInterville=$em->getRepository("AppBundle:InterVillePrices");
        $interville=$repInterville->findOneByLocation($location);
        dump($interville);

        //Récupérer la liste des cités à gérer
        $locations= $interville->getCitiesTable();
            dump($locations);
        die();

        return $this->redirectToRoute('list_city');
    }*/
}
