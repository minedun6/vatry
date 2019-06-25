<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\Back;

use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use AppBundle\Form\PersonAddType;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Back
 * @Route("/admin/users")
 */
class UsersController extends Controller
{

    /**
     * @Route("/list/{roleUser}",name="users_list")
     */
    public function indexAction($roleUser = null)
    {
        $types = $this->getParameter('userTypes');

        $res = array();
        if (!$roleUser) {
            foreach ($types as $type)
                $res[] = $type['key'];
        } else if ($roleUser == "b2c") {
            $res[] = "customer";
        } else if ($roleUser == "b2b") {
            $res[] = "";
        } else if ($roleUser == "associate") {
            $res[] = "associate";
        } else if ($roleUser == "utilisateur") {
            $res[] = "agent";
            $res[] = "agentAdmin";
            $res[] = "secretary";
        }
        $users = $this->getDoctrine()->getRepository("AppBundle:User")->getUsersByType($res);

        return $this->render("@App/Back/Users/list.html.twig", array(
            'users' => $users
        ));
    }

    /**
     * @param String $role
     * @return Response
     * @Route("/list/{role}",name="users_by_role")
     */
    public function usersByRoleAction($role)
    {

        $users = $this->getDoctrine()->getRepository("AppBundle:User")->getUserByRole($role);

        return $this->render("@App/Back/Users/list.html.twig", array(
            'users' => $users
        ));
    }

    /**
     * @param Person $person
     * @param Request $request
     * @return Response
     * @Route("/add/{person}",name="users_add")
     */
    public function newEditUser(Request $request, Person $person = null)
    {
        $em = $this->getDoctrine()->getManager();

        $new = false;
        if ($person == null) {
            $person = new Person();
            $user = new User();
            $person->setUser($user);
            $new = true;
        }

        $form = $this->createForm(PersonAddType::class, $person);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($new) {
                    if ($this->checkUserByEmail($user->getEmail())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render("@App/Back/Users/add_edit.html.twig", array(
                            'form' => $form->createView(),
                            'new' => $new
                        ));
                    }

                    $sended = $this->get('users.service')
                        ->createUserForPerson($person);

                    if (!$user->getPerson()) $user->setPerson($person);
                    $em->persist($person);
                    $em->persist($user);

                    if (!$sended) {
                        $this->addFlash('error', "Echec lors de l'envoi du mail à l'utilisateur");
                    }
                } else {
                    if ($this->checkUpdateUserByEmail($person->getUser()->getEmail(), $person->getUser()->getId())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render("@App/Back/Users/add_edit.html.twig", array(
                            'form' => $form->createView(),
                            'new' => $new
                        ));
                    }

                    /***********/
                    $userTypes = $this->getParameter('userTypes');
                    $user = $person->getUser();
                    foreach ($userTypes as $roleType) {
                        if ($roleType['key'] == $user->getType()) {
                            $role = $roleType['role'];
                        }
                    }

                    $user->setRoles([$role]);
                    /***********/
                }

                $em->flush();

                if ($new) {
                    $this->addFlash('success', 'Utilisateur ajouté avec succès');
                } else {
                    $this->addFlash('success', 'Utilisateur modifié avec succès');
                }
                return $this->redirectToRoute('users_list');
            }
        }

        return $this->render("@App/Back/Users/add_edit.html.twig", array(
            'form' => $form->createView(),
            'new' => $new
        ));
    }

    /**
     * @param Request $request
     * @param $partnerAgency
     * @return Response
     * @Route("/update_password/{partnerAgency}",name="users_update_password")
     */
    public function updateUsersPassword(Request $request, $partnerAgency = false)
    {
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('user');
            $user = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);

            if (!$user) {
                $this->addFlash('error', "Utilisateur inexistant");
                $this->addFlash('error', "Echec lors de la modification du mot de passe de l'utilisateur");
            } else {
                $password_first = $request->request->get('password_first');
                $password_second = $request->request->get('password_second');
                if ($password_second === $password_first) {
                    if (strlen($password_first) >= 8 && strlen($password_first) <= 20) {
                        $user->setSalt(Helpers::generateRandomString(30));
                        $passwordEncoded = $this->container->get('security.password_encoder')->encodePassword($user, $password_first);
                        $user->setPassword($passwordEncoded);
                        $em->flush();

                        $this->addFlash('success', 'Mot de passe modifié avec succès');
                    } else $this->addFlash('error', "Mot de passe invalide");

                } else $this->addFlash('error', "Mots de passes non identiques");
            }

        }
//var_dump($partnerAgency);die;
        if ($partnerAgency)
            return $this->redirectToRoute('liste_agence');
        else
            return $this->redirectToRoute('users_list');
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

    /**
     * @param Person $person
     * @param Request $request
     * @return Response
     * @Route("/delete/{person}",name="users_delete")
     */
    public function deleteAgent(Request $request, Person $person = null)
    {
        return $this->redirectToRoute('agents_list');
    }

    /**
     * @param Person $person
     * @param Request $request
     * @return Response
     * @Route("/orders/{$person}",name="users_orders")
     */
    public function orderPerson(Request $request, Person $person = null)
    {
        return $this->redirectToRoute('users_list');
    }
}