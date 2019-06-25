<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 04/07/2016
 * Time: 12:01
 */

namespace AppBundle\Controller\Back;
use AppBundle\Entity\InterVillePrices;
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
class IntervillePriceController extends Controller
{
    /**
     * @param Request $request
     * @Route("/newinterville",name="new_interville")
     */
    public function newAction(Request $request)
    {
        $intervp = new InterVillePrices();
        $form = $this->createForm('AppBundle\Form\Back\IntervillePriceType', $intervp,array('action' => '#','attr' => array('id' => 'addForm')))
            ->add('submit', SubmitType::class, array('label' => 'Create'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($intervp);
            $em->flush();

            return new JsonResponse(array($intervp->getId(),$intervp->getLocation()->getName(),$intervp->getZipCode(),$intervp->getRdv(),$intervp->getAdultePrice(),$intervp->getChildPrice(),$intervp->getBabyPrice(),$intervp->getDuration(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$intervp->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$intervp->getId().',this)"></span>'));
        }

        return new JsonResponse(array('html' => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'intervp' => $intervp,
            'form' => $form->createView(),
        ))->getContent()));
    }

    /**
     * Finds and displays a Flight entity.
     *
     */
    /**
     * @param Request $request
     * @Route("/editinterville",name="edit_interville")
     */
    public function editAction(Request $request)
    {   $id=$request->get('id');
        $intervp=$this->getDoctrine()->getRepository('AppBundle:InterVillePrices')->find($id);
        $editForm = $this->createForm('AppBundle\Form\Back\IntervillePriceType', $intervp,array(
            'action' => '#','attr' => array('id' => 'updateForm')))
            ->add('submit', SubmitType::class, array('label' => 'Modifier'));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($intervp);
            $em->flush();
            return new JsonResponse(array($intervp->getId(),$intervp->getLocation()->getName(),$intervp->getZipCode(),$intervp->getRdv(),$intervp->getAdultePrice(),$intervp->getChildPrice(),$intervp->getBabyPrice(),$intervp->getDuration(),'<span class="glyphicon glyphicon-pencil opicon" onclick="updateRow('.$intervp->getId().',this)"></span><span class="glyphicon glyphicon-trash opicon" onclick="deleteRow('.$intervp->getId().',this)"></span>'));
        }

        return new JsonResponse(array("html" => $this->render('@App/Back/DBParametrage/Forms/bsform.html.twig', array(
            'intervp' => $intervp,
            'form' => $editForm->createView(),
        ))->getContent()));
    }

    /**
     * @param Request $request
     * @Route("/deleteinterville",name="delete_interville")
     */
    public function deleteAction(Request $request)
    {   $id=$request->get('id');
        $intervp=$this->getDoctrine()->getRepository('AppBundle:InterVillePrices')->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($intervp);
        $em->flush();
        return new JsonResponse('ok');
    }

}