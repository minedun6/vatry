<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 04/07/2016
 * Time: 12:01
 */

namespace AppBundle\Controller\Back;

use AppBundle\Entity\Flight;
use AppBundle\Form\Back\FlightType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
/**
 * @Route("/admin")
 */
class FlightController extends Controller
{
    /**
     * @param Request $request
     * @Route("/newflight",name="new_flight")
     */
    public function newAction(Request $request)
    {
        $flight = new Flight();
        $form = $this->createForm('AppBundle\Form\Back\FlightType', $flight, array('action' => '#', 'attr' => array('id' => 'addForm')))
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($flight);
            $em->flush();

            return new JsonResponse(array($flight->getId(), $flight->getNum(), $flight->getFromLocation(), $flight->getToLocation(), $flight->getTime()->format('H:m Y/m/d'), $flight->getCountry(), '<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow(' . $flight->getId() . ',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow(' . $flight->getId() . ',this)"></span>'));
        }

        return new JsonResponse(array('html' => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'flight' => $flight,
            'form' => $form->createView(),
        ))->getContent()));
    }

    /**
     * Finds and displays a Flight entity.
     *
     */
    /**
     * @param Request $request
     * @Route("/editflight",name="edit_flight")
     */
    public function editAction(Request $request)
    {
        $id = $request->get('id');
        $flight = $this->getDoctrine()->getRepository('AppBundle:Flight')->find($id);
        $editForm = $this->createForm('AppBundle\Form\Back\FlightType', $flight, array(
            'action' => '#', 'attr' => array('id' => 'updateForm')))
            ->add('submit', SubmitType::class, array('label' => 'Modifier'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($flight);
            $em->flush();
            return new JsonResponse(array($flight->getId(), $flight->getNum(), $flight->getFromLocation(), $flight->getToLocation(), $flight->getTime()->format('H:m Y/m/d'), $flight->getCountry(), '<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow(' . $flight->getId() . ',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow(' . $flight->getId() . ',this)"></span>'));
        }

        return new JsonResponse(array("html" => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'flight' => $flight,
            'form' => $editForm->createView(),
        ))->getContent()));
    }

    /**
     * @param Request $request
     * @Route("/deleteflight",name="delete_flight")
     */
    public function deleteAction(Request $request)
    {
        try {
            $id = $request->get('id');
            $flight = $this->getDoctrine()->getRepository('AppBundle:Flight')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($flight);
            $em->flush();
            return new JsonResponse('ok');
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse('Impossible de supprimer cet element (Foreign key error)', 500);
        } catch (Exception $e) {
            return new JsonResponse('Oups! Un probleme est survenu', 500);

        }
    }

}