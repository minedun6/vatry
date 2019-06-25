<?php

namespace AppBundle\Controller\PartnerAgency;

use AppBundle\Entity\Agence;
use AppBundle\Entity\User;
use AppBundle\Entity\AgencePartner;
use AppBundle\Form\AgenceType;
use AppBundle\Form\User\PasswordType;
use AppBundle\Form\AgencePartnerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller\PartnerAgency
 * @Route("/part_agency")
 */
class AccountController extends Controller {

    /**
    * @param Request $request
    * @return Response
    * @Route("/account_part_ag",name="partner_agency_account")
    */
    public function accountAction(Request $request){
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $partnerAgency = $user->getAgencePartner();
        $agency = $partnerAgency->getAgence();

        $formPartner = $this->createForm(AgencePartnerType::class,$partnerAgency);
        $formPartner->remove('user');
//        $passwordForm = $this->createForm(PasswordType::class);
        $passwordForm = $this->createForm(PasswordType::class, null, array(
            'user' => $this->getUser()
        ));

        $render = "@App/PartnerAgency/account.html.twig";

        if ($request->getMethod() == 'POST'){
            $formPartner->handleRequest($request);

            if ($formPartner->isValid()){
                if($this->checkAgencyByEmail($agency->getEmail(), $agency->getId())){
                    $this->addFlash('error',"Un compte avec cette adresse mail existe !");

                    return $this->render($render,array(
                        'formPartner' => $formPartner->createView(),
                        'passwordForm' => $passwordForm->createView()
                    ));
                }
                $em->flush();

                $this->addFlash('success','Données compte/agence partenaire modifiées avec succès');

//                return $this->redirectToRoute('partner_agency_account');
                return $this->render($render,array(
                    'formPartner' => $formPartner->createView(),
                    'passwordForm' => $passwordForm->createView()
                ));
            }
        }

        return $this->render($render,array(
            'formPartner' => $formPartner->createView(),
            'passwordForm' => $passwordForm->createView()
        ));
    }

    /**
     * @Route("/modify_password_part_ag",name="modify_password_part_agency")
     * @Method("post")
     */
    public function modifyPasswordPA(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        $passwordForm = $this->createForm(PasswordType::class, null, array(
            'user' => $this->getUser()
        ));

        $user = $this->getUser();
        $partnerAgency = $user->getAgencePartner();

        $formPartner = $this->createForm(AgencePartnerType::class,$partnerAgency);

        $render = "@App/PartnerAgency/account.html.twig";

        $passwordForm->handleRequest($request);

        if ($passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $newPassword = $this->get('security.password_encoder')->encodePassword($this->getUser(), $data['new_password']);
            $this->getUser()->setPassword($newPassword);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success',"Mot de passe changé avec succès");

            return $this->redirectToRoute("partner_agency_account");

        }else{
            $this->get('session')->getFlashBag()->add('error',"Erreur lors de la modification de la mot de passe");
        }

        return $this->render($render,array(
            'formPartner' => $formPartner->createView(),
            'passwordForm' => $passwordForm->createView()
        ));

    }

    public function checkAgencyByEmail($email, $id = null){
        $agency = $this->getDoctrine()->getRepository("AppBundle:Agence")->findOneByEmail($email);

        if($id)
            $agencyToUpdate = $this->getDoctrine()->getRepository("AppBundle:Agence")->findOneById($id);

        if($agency) {
            if($id){
                if($agency == $agencyToUpdate) return false;
                return true;
            }
            else return true;
        }
        else return false;
    }

}