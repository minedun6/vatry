<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 30/04/2016
 * Time: 12:01
 */

namespace AppBundle\Controller\User;


use AppBundle\Entity\Transfer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CientController
 * @package AppBundle\Controller\User
 * @Route("/client")
 */
class ClientController extends Controller
{

    /**
     * @Route("/home",name="home_user")
     */
    public function homeAction()
    {

        return $this->redirectToRoute('user_transfers');
    }

    /**
     * @Route("/transfers",name="user_transfers")
     */
    public function transfersAction()
    {
        $types = array(Transfer::STATUS_PAID, Transfer::STATUS_CANCEL, Transfer::STATUS_PAID_PENDING);
        $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")->findBy(
            array('createdBy' => $this->getUser()->getId(), 'status' => $types),
            array('createdAt' => 'desc'),
            null,
            null);

        return $this->render("@App/User/Client/transfers.html.twig", array(
            'transfers' => $transfers
        ));
    }

    /**
     * @Route("/transfer_details/{transfer}",name="transfer_details")
     */
    public function transferDetailsAction(Transfer $transfer=null)
    {
        if($transfer!= null && $this->getUser()==$transfer->getCreatedBy())
        {
        return $this->render("@App/User/Client/transfer_details.html.twig", array(
            'transfer' => $transfer
        ));
        }
        else{
            $url = $this->get('router')->generate('homepage');
            Return $this->redirect($url);
        }
    }

    /**
     * @param Transfer $transfer
     * @return JsonResponse
     * @Route("/cancel_transfer/{transfer}",name="cancel_transfer",options={"expose"=true})
     */
    public function cancelTransfer(Request $request, Transfer $transfer=null)
    {

        $form = $this->createFormBuilder()->getForm();
        $is_relay_customer = in_array('ROLE_RELAY_CUSTOMER', $this->getUser()->getRoles());
        if ($request->getMethod() == 'POST' && !$is_relay_customer) {
            $form->handleRequest($request);

            if ($transfer->getCreatedBy() == $this->getUser() &&
                !in_array($transfer->getStatus(), [Transfer::STATUS_CANCEL, Transfer::STATUS_PAID])
            ) {
                $transfer->setStatus(Transfer::STATUS_CANCEL);
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', "Commande annulée avec succès");

                return $this->redirectToRoute("transfer_details", array(
                    'transfer' => $transfer->getId()
                ));
            }

        } else {
            return new JsonResponse(array(
                'html' => $this->renderView("@App/User/Client/cancel_form.html.twig", array(
                    'form' => $form->createView(),
                    'transfer' => $transfer
                ))
            ));
        }
    }

}