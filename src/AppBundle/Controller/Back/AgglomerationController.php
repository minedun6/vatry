<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 04/07/2016
 * Time: 12:01
 */

namespace AppBundle\Controller\Back;
use Proxies\__CG__\AppBundle\Entity\Agglomeration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
/**
 * @Route("/admin")
 */
class AgglomerationController extends Controller
{
    /**
     * @param Request $request
     * @Route("/new_agglomeration",name="new_agglomeration")
     */
    public function newAction(Request $request)
    {
        $agglomeration = new Agglomeration();
        if($request->getMethod()=='GET')
        {   $last=$this->getDoctrine()->getRepository('AppBundle:Agglomeration')->findOneBy(array(),array('id' => 'DESC'));
            $agglomeration->setId($last->getId()+1);
        }
        $form = $this->createForm('AppBundle\Form\Back\AgglomerationType', $agglomeration,array('action' => '#','attr' => array('id' => 'addForm')))
            ->add('submit', SubmitType::class, array('label' => 'Create'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($agglomeration);
            $em->flush();
            }
            catch (UniqueConstraintViolationException $e)
            {
                return new JsonResponse('L\'ID existe déja, veuillez le changer SVP (Primary key error)',500);
            }

            return new JsonResponse(array($agglomeration->getId(),$agglomeration->getName(),$agglomeration->getLat(),$agglomeration->getLng(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$agglomeration->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$agglomeration->getId().',this)"></span>'));
        }

        return new JsonResponse(array('html' => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'flight' => $agglomeration,
            'form' => $form->createView(),
        ))->getContent()));
    }

    /**
     * Finds and displays a Flight entity.
     *
     */
    /**
     * @param Request $request
     * @Route("/edit_agglomeration",name="edit_agglomeration")
     */
    public function editAction(Request $request)
    {   $id=$request->get('id');
        $agglomeration=$this->getDoctrine()->getRepository('AppBundle:Agglomeration')->find($id);
        $editForm = $this->createForm('AppBundle\Form\Back\AgglomerationType', $agglomeration,array(
            'action' => '#','attr' => array('id' => 'updateForm')))
            ->add('submit', SubmitType::class, array('label' => 'Modifier'));

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $agglomeration->setId($id);
            $em = $this->getDoctrine()->getManager();
            $em->persist($agglomeration);
            $em->flush();
            return new JsonResponse(array($agglomeration->getId(),$agglomeration->getName(),$agglomeration->getLat(),$agglomeration->getLng(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$agglomeration->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$agglomeration->getId().',this)"></span>'));
        }
        $editForm->remove('id');
        return new JsonResponse(array("html" => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'flight' => $agglomeration,
            'form' => $editForm->createView(),
        ))->getContent()));
    }

    /**
     * @param Request $request
     * @Route("/delete_agglomeration",name="delete_agglomeration")
     */
    public function deleteAction(Request $request)
    {   $id=$request->get('id');
        try {
            $agglomeration = $this->getDoctrine()->getRepository('AppBundle:Agglomeration')->find($id);
            $em = $this->getDoctrine()->getManager();
            $em->remove($agglomeration);
            $em->flush();
            return new JsonResponse('ok');
        }
        catch (ForeignKeyConstraintViolationException $e)
        {
            return new JsonResponse('Impossible de supprimer cet element (Foreign key error)',500);
        }
        catch(Exception $e)
        {
            return new JsonResponse('Oups! Un probleme est survenu',500);

        }


    }

}