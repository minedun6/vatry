<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 19/06/2016
 * Time: 18:28
 */

namespace AppBundle\Controller\Partner;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Transfer;

/**
 * Class DefaultPartnerController
 * @package AppBundle\Controller\Partner
 * @Route("/partner")
 */
class DefaultPartnerController extends Controller
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/list_transfer/{type}",name="list_transfer_for_partner")
     */
    public function transferList($type = null)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        $parent = $this->get('common.back.service')->getMenuExtends();
        $archived = false;
        $em = $this->getDoctrine()->getRepository('AppBundle:Transfer');
        $user = $this->getUser();
        $partner = $user->getPartner();

        if ((isset($partner) && $partner->getIsAirport()) || in_array('ROLE_ASSOCIATE', $user->getRoles())) {
            if ($type == 'archive') {
                $status = [Transfer::STATUS_PAID, Transfer::STATUS_PAID_B2B];
                $transfers = $em->archivedTransfer(null, $status);
                $archived = true;
            } else {
                $status = [Transfer::STATUS_PAID, Transfer::STATUS_PAID_B2B];
                $transfers = $em->currentTransfer(null, $status);
            }

        } else {
            if ($type == 'archive') {
                $status = [Transfer::STATUS_PAID];
                $transfers = $em->archivedTransfer(null, $status);
                $archived = true;
            } else {
                $status = [Transfer::STATUS_PAID];
                $transfers = $em->currentTransfer(null, $status);
            }
        }

        return $this->render("@App/Partner/transfer_list.html.twig", array(
            'transfers' => $transfers,
            'is_archived' => $archived,
            'all' => true,
            'parent' => $parent
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/",name="partners_index")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('my_transfers');
    }


    /**
     * @Route("/mes-commandes/{type}", name="my_transfers")
     *
     */
    public function getMyCommands($type = null)
    {
        if (in_array('ROLE_ASSOCIATE', $this->getUser()->getRoles()))
            return $this->redirectToRoute('homepage');

        $status = [Transfer::STATUS_PAID, Transfer::STATUS_PAID_B2B];
        $parent = $this->get('common.back.service')->getMenuExtends();
        $archived = false;
        $partner = $this->getUser()->getPartner();
        $em = $this->getDoctrine()->getRepository('AppBundle:Transfer');
        if ($type == 'archive') {
            $transfers = $em->archivedTransfer($partner->getId(), $status);
            $archived = true;
        } else {
            $transfers = $em->currentTransfer($partner->getId(), $status);
        }


        return $this->render("@App/Partner/transfer_list.html.twig", array(
            'transfers' => $transfers,
            'is_archived' => $archived,
            'parent' => $parent
        ));
    }


    private function __has_access()
    {
        $allowed_roles = ['ROLE_PARTNER', 'ROLE_ASSOCIATE'];
        $user = $this->getUser();
        if ($user != null) {
            if (!in_array($user->getRoles()[0], $allowed_roles)) {
                return false;
            }

            return true;
        }

        return false;
    }


}