<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 19/06/2016
 * Time: 11:40
 */

namespace AppBundle\Controller\Back;


use AppBundle\Entity\Partner;
use AppBundle\Entity\User;
use AppBundle\Form\PartnerType;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PartnerController
 * @package AppBundle\Controller\Back
 * @Route("/admin/partners")
 */
class PartnerController extends Controller {

    /**
     * @Route("/list",name="partners_list")
     */
    public function indexAction(){

        $partners = $this->getDoctrine()
            ->getRepository("AppBundle:Partner")
            ->findAll();

        return $this->render("@App/Back/Partners/list.html.twig",array(
            'partners' => $partners
        ));
    }

    /**
     * @param Partner $partner
     * @param Request $request
     * @return Response
     * @Route("/add/{partner}",name="partners_add")
     */
    public function newEditPartner(Request $request,Partner $partner = null){

        $new = false;
        if ($partner == null){
            $partner = new Partner();
            $user = new User();
            $partner->addUser($user);
            $new = true;
        }

        $form = $this->createForm(PartnerType::class,$partner);

        if ($request->getMethod() == 'POST'){
            $form->handleRequest($request);
            if ($form->isValid()){
                if ($new){
                    $this->getDoctrine()->getManager()->persist($partner);

                    if($this->checkUserByEmail($user->getEmail())){
                        $this->addFlash('error',"Un compte avec cette adresse mail existe !");

                        return $this->render("@App/Back/Partners/add_edit.html.twig",array(
                            'form' => $form->createView(),
                            'new' => $new
                        ));
                    }

                    $sended = $this->get('partners.service')
                        ->createUserForPartner($partner);
                    if (!$sended){
                        $this->addFlash('error',"Echec lors de l'envoi du mail au partenaire");
                    }
                }else{
                    if($this->checkUpdateUserByEmail($partner->getPrincipalUser()->getEmail(), $partner->getPrincipalUser()->getId())){
                        $this->addFlash('error',"Un compte avec cette adresse mail existe !");

                        return $this->render("@App/Back/Partners/add_edit.html.twig",array(
                            'form' => $form->createView(),
                            'new' => $new
                        ));
                    }
                }

                $this->getDoctrine()->getManager()->flush();

                if ($new){
                    $this->addFlash('success','Partenaire ajouté avec succès');
                }else{
                    $this->addFlash('success','Partenaire modifié avec succès');
                }
                return $this->redirectToRoute('partners_list');
            }
        }

        return $this->render("@App/Back/Partners/add_edit.html.twig",array(
            'form' => $form->createView(),
            'new' => $new
        ));
    }


    public function checkUserByEmail($email){
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneByEmail($email);
        if($user) return true;
        else return false;
    }

    public function checkUpdateUserByEmail($email, $id){
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneByEmail($email);
        $userToUpdate = $this->getDoctrine()->getRepository("AppBundle:User")->findOneById($id);

        if($user){
            if($user == $userToUpdate) return false;

            return true;
        }
        else return false;
    }

    /**
     * @param Partner $partner
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/generate_url/{partner}",name="generate_url_partner")
     */
    public function generateNewUrl(Partner $partner){

        $this->get('partners.service')->generateTokenForPartner($partner);
        $this->addFlash('success','Url générée avec succès');
        return $this->redirectToRoute("partners_list");
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/update_password",name="partners_update_password")
     */
    public function updateUsersPassword(Request $request){
        if ($request->getMethod() == 'POST'){
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('user');
            $user = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);

            if (!$user){
                $this->addFlash('error',"Partenaire inexistant");
//                $this->addFlash('error',"Echec lors de la modification du mot de passe du partenaire");
            }else{
                $password_first = $request->request->get('password_first');
                $password_second = $request->request->get('password_second');
                if ($password_second === $password_first ){
                    if( strlen($password_first) >= 8 && strlen($password_first) <= 20 ){
                        $user->setSalt(Helpers::generateRandomString(30));
                        $passwordEncoded = $this->container->get('security.password_encoder')->encodePassword($user,$password_first);
                        $user->setPassword($passwordEncoded);
                        $em->flush();

                        $this->addFlash('success','Mot de passe modifié avec succès');
                    }else $this->addFlash('error',"Mot de passe invalide");

                }else $this->addFlash('error',"Mots de passes non identiques");
            }

        }

        return $this->redirectToRoute('partners_list');
    }


}