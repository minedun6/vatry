<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 19/04/2016
 * Time: 22:24
 */

namespace AppBundle\Controller\Security;



use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use AppBundle\Form\PersonType;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller {

    /**
     * @Route("/login",name="login",options={"expose"=true})
     */
    public function loginAction(Request $request){

        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute("homepage");
        }

        return $this->render("@App/Security/login.html.twig");

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/registration",name="registration",options={"expose"=true})
     */
    public function registerAction(Request $request){

        $person = new Person();
        $form = $this->createForm(PersonType::class,$person);

        if ($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            $user = $this->getDoctrine()->getRepository("AppBundle:User")
                ->findOneBy(array(
                    'email'=> $person->getEmail()
                ));

            if ($user){
                $form->get('email')->addError(New FormError("Il existe un autre utilisateur avec le même e-mail"));
            }

            if ($form->isValid()){
                $this->getDoctrine()->getManager()->persist($person);

                $user = new User();
                $user
                    ->setRoles(array('ROLE_CUSTOMER'))
                    ->setType('customer')
                    ->setEmail($person->getEmail())
                    ->setPassword('password')
                    ->setPerson($person);
                $this->getDoctrine()->getManager()->persist($user);

                //generating salt
                $salt = Helpers::generateRandomString(30);
                $user->setSalt($salt);

                //generating password
                $plainPassword = Helpers::generateRandomString(8);

                //sending password to user
                $this->get('user.security.service')->sendUserPassword($user,$plainPassword);

                //encoding password
                $encodedPassword = $this->get('user.security.service')->encodeUserPassword($user,$plainPassword);

                //saving password
                $user->setPassword($encodedPassword);

                $this->getDoctrine()->getManager()->flush();

                if ($request->isXmlHttpRequest()){
                    return new JsonResponse(array(
                        'status' => true
                    ));
                }
            }else{
                if ($request->isXmlHttpRequest()){
                    return new JsonResponse(array(
                        'status' => false,
                        'html'=> $this->renderView("@App/Security/register_form.html.twig",array(
                            'form' => $form->createView()
                        ))
                    ));
                }
            }
        }

        return $this->render("@App/Security/register_form.html.twig",array(
            'form' => $form->createView()
        ));

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/password_forgotten",name="password_forgotten")
     */
    public function passwordForgottenAction(Request $request){

        $form = $this->createFormBuilder()
            ->add('email',EmailType::class)
            ->getForm();


        if ($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            $email = $form->get('email')->getData();

            $user = $this->getDoctrine()->getRepository("AppBundle:User")
                ->findOneBy(array(
                    'email' => $email
                ));

            if ($user){
                //generating password
                $plainPassword = Helpers::generateRandomString(8);

                //sending password to user
                $this->get('user.security.service')->sendUserNewPassword($user,$plainPassword);

                //encoding password
                $encodedPassword = $this->get('user.security.service')->encodeUserPassword($user,$plainPassword);

                //saving password
                $user->setPassword($encodedPassword);

                $this->getDoctrine()->getManager()->flush();

                $this->get('session')->getFlashBag()->add('success',"Votre mot de passe est réinitialisé avec succès. Veuillez consulter votre boite mail");

                return $this->redirectToRoute("login");
            }else{
                $this->get('session')->getFlashBag()->add('error',"Aucun utilisateur trouvé avec l'email indiqué ");
            }
        }

        return $this->render("@App/Security/forgotten_password.html.twig",array(
            'form'=> $form->createView()
        ));
    }

}