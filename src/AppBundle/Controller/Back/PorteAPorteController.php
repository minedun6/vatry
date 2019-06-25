<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 04/07/2016
 * Time: 12:01
 */

namespace AppBundle\Controller\Back;
use AppBundle\Entity\PorteAPortePrice;
use AppBundle\Form\Back\FlightType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
/**
 * @Route("/admin")
 */
class PorteAPorteController extends Controller
{
    /**
     * @param Request $request
     * @Route("/newporteaporte",name="new_porteaporte")
     */
    public function newAction(Request $request)
    {
        $porteaporte = new PorteAPortePrice();
        $form = $this->createForm('AppBundle\Form\Back\PorteAPorteType', $porteaporte,array('action' => '#','attr' => array('id' => 'addForm')))
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($porteaporte);
            $em->flush();
            return new JsonResponse(array($porteaporte->getId(),$porteaporte->getLocation()->getName(),$porteaporte->getAgglomeration()->getName(),$porteaporte->getZipCode(),$porteaporte->getPrice(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$porteaporte->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$porteaporte->getId().',this)"></span>'));
        }

        return new JsonResponse(array('html' => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'porteaporte' => $porteaporte,
            'form' => $form->createView(),
        ))->getContent()));
    }

    /**
     * Finds and displays a Flight entity.
     *
     */
    /**
     * @param Request $request
     * @Route("/editporteaporte",name="edit_porteaporte")
     */
    public function editAction(Request $request)
    {   $id=$request->get('id');
        $porteaporte=$this->getDoctrine()->getRepository('AppBundle:PorteAPortePrice')->find($id);
        $editForm = $this->createForm('AppBundle\Form\Back\PorteAPorteType', $porteaporte,array(
            'action' => '#','attr' => array('id' => 'updateForm')))
            ->add('submit', SubmitType::class, array('label' => 'Modifier'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($porteaporte);
            $em->flush();
            return new JsonResponse(array($porteaporte->getId(),$porteaporte->getLocation()->getName(),$porteaporte->getAgglomeration()->getName(),$porteaporte->getZipCode(),$porteaporte->getPrice(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$porteaporte->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$porteaporte->getId().',this)"></span>'));
        }

        return new JsonResponse(array("html" => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'porteaporte' => $porteaporte,
            'form' => $editForm->createView(),
        ))->getContent()));
    }

    /**
     * @param Request $request
     * @Route("/deleteporteaporte",name="delete_porteaporte")
     */
    public function deleteAction(Request $request)
    {   $id=$request->get('id');
        $porteaporte=$this->getDoctrine()->getRepository('AppBundle:PorteAPortePrice')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($porteaporte);
        $em->flush();
        return new JsonResponse('ok');
    }

}