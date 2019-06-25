<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 04/07/2016
 * Time: 12:01
 */

namespace AppBundle\Controller\Back;

use AppBundle\Entity\Location;
use AppBundle\Form\Back\FlightType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

/**
 * @Route("/admin")
 */
class LocationController extends Controller
{
    /**
     * @param Request $request
     * @Route("/newlocation",name="new_location")
     */
    public function newAction(Request $request)
    {
        $location = new Location();
        $form = $this->createForm('AppBundle\Form\Back\LocationType', $location, array('action' => '#', 'attr' => array('id' => 'addForm')))
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();

            return new JsonResponse(array($location->getId(), $location->getName(), $location->getZipCode(), $location->getType(), $location->getLat(), $location->getLng(), '<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow(' . $location->getId() . ',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow(' . $location->getId() . ',this)"></span>'));
        }

        return new JsonResponse(array('html' => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'location' => $location,
            'form' => $form->createView(),
        ))->getContent()));
    }

    /**
     * Finds and displays a Flight entity.
     *
     */
    /**
     * @param Request $request
     * @Route("/editlocation",name="edit_location")
     */
    public function editAction(Request $request)
    {
        $id = $request->get('id');
        $location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($id);
        $editForm = $this->createForm('AppBundle\Form\Back\LocationType', $location, array(
            'action' => '#', 'attr' => array('id' => 'updateForm')))
            ->add('submit', SubmitType::class, array('label' => 'Modifier'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($location);
            $em->flush();
            return new JsonResponse(array($location->getId(), $location->getName(), $location->getZipCode(), $location->getType(), $location->getLat(), $location->getLng(), '<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow(' . $location->getId() . ',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow(' . $location->getId() . ',this)"></span>'));
        }

        return new JsonResponse(array("html" => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'flight' => $location,
            'form' => $editForm->createView(),
        ))->getContent()));
    }

    /**
     * @param Request $request
     * @Route("/deletelocation",name="delete_location")
     */
    public function deleteAction(Request $request)
    {
        try {
            $id = $request->get('id');
            $location = $this->getDoctrine()->getRepository('AppBundle:Location')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($location);
            $em->flush();
            return new JsonResponse('ok');
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse('Impossible de supprimer cet element (Foreign key error)', 500);
        } catch (Exception $e) {
            return new JsonResponse('Oups! Un probleme est survenu', 500);

        }
    }

    /**
     * @Route("/parametrage/location/load",name="location_datasource")
     */

    public function getDataLocation(Request $request)
    {
        $length = $request->get('length');
        $length = $length && ($length != -1) ? $length : 0;

        $start = $request->get('start');
        $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;

        $search = $request->get('search');
        $filters = [
            'query' => @$search['value']
        ];

        $locations = $this->getDoctrine()->getRepository('AppBundle:Location')->search(
            $filters, $start, $length
        );
        ini_set("memory_limit", "-1");
        $output = array(
            'data' => array(),
            'recordsFiltered' => count($this->getDoctrine()->getRepository('AppBundle:Location')->search($filters, 0, false)),
            'recordsTotal' => count($this->getDoctrine()->getRepository('AppBundle:Location')->search(array(), 0, false))
        );

        foreach ($locations as $location) {
            $output['data'][] = [
                'id' => $location->getId(),
                'name' => $location->getName(),
                'zipCode' => $location->getZipCode(),
                'type' => $location->getType(),
                'lat' => $location->getLat(),
                'lng' => $location->getLng(),
                'action' => '<span class="glyphicon glyphicon-pencil opicon" aria-hidden="true" onclick="updateRow(' . $location->getId() . ',this)"></span>
                             <span class="glyphicon glyphicon-trash opicon" aria-hidden="true" onclick="deleteRow(' . $location->getId() . ',this)"></span>'
            ];
        }

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);

    }

}