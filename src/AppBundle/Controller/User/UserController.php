<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 30/04/2016
 * Time: 19:43
 */

namespace AppBundle\Controller\User;


use AppBundle\Form\PersonType;
use AppBundle\Form\User\PasswordType;
use AppBundle\Form\PartnerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package AppBundle\Controller\User
 * @Route("/user")
 */
class UserController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/account",name="account")
     */
    public function accountAction(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        $form = $this->createForm(PersonType::class, $this->getUser()->getPerson());
        $form->remove('email');

        $passwordForm = $this->createForm(PasswordType::class);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->get('session')->getFlashBag()->add('success',"Profil mis à jour avec succès");
                $this->getDoctrine()->getManager()->flush();
            }
        }

        $render = "@App/User/Profile/account.html.twig";

        if ($securityContext->isGranted('ROLE_ADMIN'))
            $render = "@App/Back/Profile/account.html.twig";
        elseif($securityContext->isGranted('ROLE_SECRETARY') || $securityContext->isGranted('ROLE_AGENT_ADMIN') || $securityContext->isGranted('ROLE_AGENT'))
            $render = "@App/Agent/Profile/account.html.twig";

        return $this->render($render,
            array(
                'form' => $form->createView(),
                'passwordForm' => $passwordForm->createView()
            ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/partner_account",name="account_partner")
     */
    public function accountPartnerAction(Request $request)
    {

        $form = $this->createForm(PartnerType::class, $this->getUser()->getPartner());
        $form->remove('email');
        $form->remove('isAirport');
        $form->remove('users');


        $passwordForm = $this->createForm(PasswordType::class);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $user = $this->getUser();

                if($this->checkUpdateUserByEmail($user->getEmail())){
                    $this->addFlash('error',"Un compte avec cette adresse mail existe !");

                    return $this->render("@App/Partner/Profile/account.html.twig",array(
                        'form' => $form->createView(),
                        'passwordForm' => $passwordForm->createView()
                    ));
                }

                $this->get('session')->getFlashBag()->add('success',"Profil mis à jour avec succès");
                $this->getDoctrine()->getManager()->flush();
            }
        }

        return $this->render("@App/Partner/Profile/account.html.twig",
            array(
                'form' => $form->createView(),
                'passwordForm' => $passwordForm->createView()
            ));
    }

    public function checkUpdateUserByEmail($email){
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneByEmail($email);
        $userToUpdate = $this->getUser();

        if($user){
            if($user == $userToUpdate) return false;

            return true;
        }
        else return false;
    }

    /**
     * @Route("/modify_password",name="modify_password")
     * @Method("post")
     */
    public function modifyPassword(Request $request)
    {
        $securityContext = $this->container->get('security.authorization_checker');

        $passwordForm = $this->createForm(PasswordType::class, null, array(
            'user' => $this->getUser()
        ));

        $passwordForm->handleRequest($request);

        if ($passwordForm->isValid()) {
            $data = $passwordForm->getData();
            $newPassword = $this->get('security.password_encoder')->encodePassword($this->getUser(), $data['new_password']);
            $this->getUser()->setPassword($newPassword);
            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success',"Mot de passe changé avec succès");

            if($securityContext->isGranted('ROLE_ADMIN') )
                return $this->redirectToRoute("account");

            if($securityContext->isGranted('ROLE_PARTNER') )
                return $this->redirectToRoute("account_partner");
//            elseif($securityContext->isGranted('ROLE_PARTNER_AGENCY') )
//                return $this->redirectToRoute("partner_agency_account");
            else
                return $this->redirectToRoute("account");

        }else{
            $this->get('session')->getFlashBag()->add('error',"Erreur lors de la modification de la mot de passe");
        }

        $form = $this->createForm(PersonType::class, $this->getUser()->getPerson());
        $form->remove('email');

        $render = "@App/User/Profile/account.html.twig";

        if ($securityContext->isGranted('ROLE_ADMIN'))
            $render = "@App/Back/Profile/account.html.twig";
        elseif($securityContext->isGranted('ROLE_SECRETARY') || $securityContext->isGranted('ROLE_AGENT_ADMIN') || $securityContext->isGranted('ROLE_AGENT'))
            $render = "@App/Agent/Profile/account.html.twig";
//        elseif($securityContext->isGranted('ROLE_PARTNER_AGENCY') )
//            return $this->redirectToRoute("partner_agency_account");
        elseif($securityContext->isGranted('ROLE_PARTNER') ){
            $form = $this->createForm(PartnerType::class, $this->getUser()->getPartner());
            $form->remove('email');

            $render = "@App/Partner/Profile/account.html.twig";
        }


        return $this->render($render,
        array(
            'form' => $form->createView(),
            'passwordForm' => $passwordForm->createView()
        ));

    }

}