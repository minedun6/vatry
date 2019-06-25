<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 04/07/2016
 * Time: 12:01
 */

namespace AppBundle\Controller\Back;
use AppBundle\Entity\PrivateLocationPrice;
use AppBundle\Form\Back\PrivateLocationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
/**
 * @Route("/admin")
 */
class PrivateLocationController extends Controller
{
    /**
     * @param Request $request
     * @Route("/newprivatelocation",name="new_privatelocation")
     */
    public function newAction(Request $request)
    {
        $privatelocation = new PrivateLocationPrice();
        $form = $this->createForm('AppBundle\Form\Back\PrivateLocationType', $privatelocation,array('attr' => array('id' => 'addForm')))
            ->add('submit', SubmitType::class, array('label' => 'Créer'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($privatelocation);
            $em->flush();
            return new JsonResponse(array($privatelocation->getId(),$privatelocation->getLocation()->getName(),$privatelocation->getPrice(),$privatelocation->getMinCapacity(),$privatelocation->getMaxCapacity(),$privatelocation->getZipCode(),$privatelocation->getDistance(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$privatelocation->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$privatelocation->getId().',this)"></span>'));
        }

        return new JsonResponse(array('html' => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'privatelocation' => $privatelocation,
            'form' => $form->createView(),
        ))->getContent()));
    }

    /**
     * Finds and displays a Flight entity.
     *
     */
    /**
     * @param Request $request
     * @Route("/editprivatelocation",name="edit_privatelocation")
     */
    public function editAction(Request $request)
    {   $id=$request->get('id');
        $privatelocation=$this->getDoctrine()->getRepository('AppBundle:PrivateLocationPrice')->find($id);
        $editForm = $this->createForm('AppBundle\Form\Back\PrivateLocationType', $privatelocation,array(
            'action' => '#','attr' => array('id' => 'updateForm')))
            ->add('submit', SubmitType::class, array('label' => 'Modifier'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($privatelocation);
            $em->flush();
            return new JsonResponse(array($privatelocation->getId(),$privatelocation->getLocation()->getName(),$privatelocation->getPrice(),$privatelocation->getMinCapacity(),$privatelocation->getMaxCapacity(),$privatelocation->getZipCode(),$privatelocation->getDistance(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$privatelocation->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$privatelocation->getId().',this)"></span>'));
        }

        return new JsonResponse(array("html" => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'privatelocation' => $privatelocation,
            'form' => $editForm->createView(),
        ))->getContent()));
    }

    /**
     * @param Request $request
     * @Route("/deleteprivatelocation",name="delete_privatelocation")
     */
    public function deleteAction(Request $request)
    {
        try {
        $id=$request->get('id');
        $privatelocation=$this->getDoctrine()->getRepository('AppBundle:PrivateLocationPrice')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($privatelocation);
        $em->flush();
        return new JsonResponse('ok');
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse('Impossible de supprimer cet element (Foreign key error)', 500);
        } catch (Exception $e) {
            return new JsonResponse('Oups! Un probleme est survenu', 500);

        }
    }
    /**
     * @Route("/parametrage/locationprivate/load",name="privatelocation_datasource")
     */

    public function getDataPl(Request $request)
    {
        $length = $request->get('length');
        $length = $length && ($length != -1) ? $length : 0;

        $start = $request->get('start');
        $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;

        $search = $request->get('search');
        $filters = [
            'query' => @$search['value']
        ];

        $privatelocations = $this->getDoctrine()->getRepository('AppBundle:PrivateLocationPrice')->search(
            $filters, $start, $length
        );
        ini_set("memory_limit", "-1");
        $output = array(
            'data' => array(),
            'recordsFiltered' => count($this->getDoctrine()->getRepository('AppBundle:PrivateLocationPrice')->search($filters, 0, false)),
            'recordsTotal' => count($this->getDoctrine()->getRepository('AppBundle:PrivateLocationPrice')->search(array(), 0, false))
        );

        foreach ($privatelocations as $privatelocation) {
            $output['data'][] = [
                'id' => $privatelocation->getId(),
                'price' => $privatelocation->getPrice(),
                'location' => $privatelocation->getLocation()->getName(),
                'zipCode' => $privatelocation->getZipCode(),
                'minCapacity' => $privatelocation->getMinCapacity(),
                'maxCapacity' => $privatelocation->getMaxCapacity(),
                'distance' => $privatelocation->getDistance(),
                'action' => '<span class="glyphicon glyphicon-pencil opicon" aria-hidden="true" onclick="updateRow(' . $privatelocation->getId() . ',this)"></span>
                             <span class="glyphicon glyphicon-trash opicon" aria-hidden="true" onclick="deleteRow(' . $privatelocation->getId() . ',this)"></span>'
            ];
        }

        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);

    }

}