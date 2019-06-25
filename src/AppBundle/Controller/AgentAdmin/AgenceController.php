<?php

namespace AppBundle\Controller\AgentAdmin;

use AppBundle\Entity\Agence;
use AppBundle\Entity\User;
use AppBundle\Entity\AgencePartner;
use AppBundle\Form\AgenceType;
use AppBundle\Form\AgencePartnerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AgenceController
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/agent-admin/agencies")
 */
class AgenceController extends Controller
{

    /**
     * @Route("/list",name="agencies_list")
     */
    public function indexAction()
    {
        $agencies = $this->getDoctrine()
            ->getRepository("AppBundle:Agence")
            ->findAll();

        $render = "@App/AgentAdmin/Agencies/list.html.twig";
        if ($this->isGranted('ROLE_ADMIN'))
            $render = "@App/Back/Agencies/list.html.twig";


        return $this->render($render, array(
            'agencies' => $agencies
        ));
    }

    /**
     * @param Agence $agency
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/details/{agency}",name="details_agency")
     */
    public function detailsTransferAction(Agence $agency)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $render = "@App/AgentAdmin/Agencies/agency_details.html.twig";
        if ($this->isGranted('ROLE_ADMIN'))
            $render = "@App/Back/Agencies/agency_details.html.twig";

        return $this->render($render, array(
            'agency' => $agency,
            'parent' =>$parent
        ));
    }



    /**
     * @param Agence $agency
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/partner-agency/{agency}/{agencePartner}/activate",name="activate_agency")
     */
    public function activateAgencyAction(Request $request,Agence $agency,AgencePartner $agencePartner)
    {
      $em = $this->getDoctrine()->getManager();

      $agencePartner->setStatus(true);
      $em->persist($agencePartner);
        $em->flush();

    return $this->redirectToRoute('liste_agence');

    }


    /**
     * @param Agence $agency
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/partner-agency/{agency}/{agencePartner}/desactivate",name="desactivate_agency")
     */
    public function desactivateAgencyAction(Request $request,Agence $agency,AgencePartner $agencePartner)
    {
      $em = $this->getDoctrine()->getManager();

      $agencePartner->setStatus(false);
      $em->persist($agencePartner);
        $em->flush();

    return $this->redirectToRoute('liste_agence');

    }





    /**
     * @param Agence $agency
     * @param Request $request
     * @return Response
     * @Route("/add/{agency}",name="agencies_add")
     */
    public function newEditAgency(Request $request, Agence $agency = null)
    {

        $em = $this->getDoctrine()->getManager();
        $parent = $this->get('common.back.service')->getMenuExtends();
        $new = false;
        if ($agency == null) {
            $agency = new Agence();
            $new = true;
        }

        $form = $this->createForm(AgenceType::class, $agency);

        $render = "@App/AgentAdmin/Agencies/add_edit.html.twig";
        if ($this->isGranted('ROLE_ADMIN'))
            $render = "@App/Back/Agencies/add_edit.html.twig";

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                if ($new) {
                    if ($this->checkAgencyByEmail($agency->getEmail())) {
                        $this->addFlash('error', "Une agence avec cette adresse mail existe !");

                        return $this->render($render, array(
                            'form' => $form->createView(),
                            'new' => $new,
                            'parent' => $parent
                        ));
                    }

                    $em->persist($agency);

                } else {
                    if ($this->checkAgencyByEmail($agency->getEmail(), $agency->getId())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render($render, array(
                            'form' => $form->createView(),
                            'new' => $new,
                            'parent' => $parent
                        ));
                    }
                }

                $em->flush();

                if ($new) {
                    $this->addFlash('success', 'Agence ajouté avec succès');
                } else {
                    $this->addFlash('success', 'Agence modifié avec succès');
                }
                return $this->redirectToRoute('liste_agence');
            }
        }

        return $this->render($render, array(
            'form' => $form->createView(),
            'new' => $new,
            'parent' => $parent
        ));
    }

    /**
     * @param Agence $agency
     * @param AgencePartner $agencePartner
     * @param Request $request
     * @return Response
     * @Route("/partner-agency/{agency}/{agencePartner}",name="partner_agency_add")
     */
    public function newEditPartnerAgency(Request $request, Agence $agency, AgencePartner $agencePartner = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em = $this->getDoctrine()->getManager();

        $new = false;
        if ($agency == null) {
            $this->addFlash('error', "Agence inexistante !");
            return $this->redirectToRoute('liste_agence');
        }
        if ($agencePartner == null) {
            $agencePartner = new AgencePartner();
            $user = new User();
            $agencePartner->setAgence($agency);
            $agencePartner->setCreatedBy($this->getUser());
            $user->setAgencePartner($agencePartner);
            $user->setType(User::TYPE_PARTNERAGENCY);
            $user->setRoles(['ROLE_PARTNER_AGENCY']);
            $user->setEmail($agency->getEmail());
            $agencePartner->setUser($user);
            $new = true;
        }

        $form = $this->createForm(AgencePartnerType::class, $agencePartner);


        $render = "@App/AgentAdmin/Agencies/add_edit_partner.html.twig";
        if ($this->isGranted('ROLE_ADMIN'))
            $render = "@App/Back/Agencies/add_edit_partner.html.twig";

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $agencePartner->setStatus(true);
                $agencePartner->setIsPrePayment(false);
                if ($new) {
                    if ($this->checkUserByEmail($agencePartner->getUser()->getEmail())) {
                        $this->addFlash('error', "Un compte avec cette adresse e-mail existe !");

                        return $this->render($render, array(
                            'form' => $form->createView(),
                            'new' => $new,
                            'parent'=>$parent
                        ));
                    }

                    $sended = $this->get('users.service')->createUserForPartnerAgency($agencePartner);

                    if (!$user->getAgencePartner()) $user->setAgencePartner($agencePartner);

                    $em->persist($agencePartner);
                    $em->persist($user);

                    if (!$sended) {
                        $this->addFlash('error', "Echec lors de l'envoi du mail à l'agence");
                    }
                } else {
                    if ($this->checkUpdateUserByEmail($agencePartner->getUser()->getEmail(), $agencePartner->getUser()->getId())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render($render, array(
                            'form' => $form->createView(),
                            'new' => $new
                        ));
                    }

//                    $agencePartner->setEmail($agencePartner->getUser()->getEmail());
                }

                $em->flush();

                if ($new) {
                    $this->addFlash('success', 'Agence devient partenaire ');
                } else {
                    $this->addFlash('success', 'Agent partenaire modifié avec succès');
                }
                return $this->redirectToRoute('liste_agence');
            }
        }

        return $this->render($render, array(
            'form' => $form->createView(),
            'new' => $new,
            'parent'=>$parent
        ));
    }

    public function checkAgencyByEmail($email, $id = null)
    {
        $agency = $this->getDoctrine()->getRepository("AppBundle:Agence")->findOneByEmail($email);

        if ($id)
            $agencyToUpdate = $this->getDoctrine()->getRepository("AppBundle:Agence")->findOneById($id);

        if ($agency) {
            if ($id) {
                if ($agency == $agencyToUpdate) return false;
                return true;
            } else return true;
        } else return false;
    }

    public function checkUserByEmail($email)
    {
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneByEmail($email);
        if ($user) return true;
        else return false;
    }

    public function checkUpdateUserByEmail($email, $id)
    {
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneByEmail($email);
        $userToUpdate = $this->getDoctrine()->getRepository("AppBundle:User")->findOneById($id);

        if ($user) {
            if ($user == $userToUpdate) return false;

            return true;
        } else return false;
    }




}
