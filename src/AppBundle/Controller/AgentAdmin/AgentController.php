<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\AgentAdmin;

use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use AppBundle\Form\AgentType;
use AppBundle\Utilities\Helpers;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller\AgentAdmin
 * @Route("/agent-admin/agents")
 */
class AgentController extends Controller
{

    /**
     * @Route("/list",name="agents_list")
     */
    public function indexAction()
    {
        $agents = $this->getDoctrine()
            ->getRepository("AppBundle:User")
            ->findByType('agent');

        return $this->render("@App/AgentAdmin/Agents/list.html.twig", array(
            'agents' => $agents
        ));
    }

    /**
     * @param Person $agent
     * @param Request $request
     * @return Response
     * @Route("/add/{agent}",name="agents_add")
     */
    public function newEditAgent(Request $request, Person $agent = null)
    {
        $em = $this->getDoctrine()->getManager();

        $new = false;
        if ($agent == null) {
            $agent = new Person();
            $user = new User();
            $user->setType('agent');
            $agent->setUser($user);
            $new = true;
        }

        $form = $this->createForm(AgentType::class, $agent);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($new) {
                    if ($this->checkUserByEmail($user->getEmail())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render("@App/AgentAdmin/Agents/add_edit.html.twig", array(
                            'form' => $form->createView(),
                            'new' => $new
                        ));
                    }

                    $sended = $this->get('users.service')
                        ->createUserForPerson($agent);

                    if (!$user->getPerson()) $user->setPerson($agent);
                    $em->persist($agent);
                    $em->persist($user);

                    if (!$sended) {
                        $this->addFlash('error', "Echec lors de l'envoi du mail à l'agent");
                    }
                } else {
                    if ($this->checkUpdateUserByEmail($agent->getUser()->getEmail(), $agent->getUser()->getId())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render("@App/AgentAdmin/Agents/add_edit.html.twig", array(
                            'form' => $form->createView(),
                            'new' => $new
                        ));
                    }
                }

                $em->flush();

                if ($new) {
                    $this->addFlash('success', 'Agent ajouté avec succès');
                } else {
                    $this->addFlash('success', 'Agent modifié avec succès');
                }
                return $this->redirectToRoute('agents_list');
            }
        }

        return $this->render("@App/AgentAdmin/Agents/add_edit.html.twig", array(
            'form' => $form->createView(),
            'new' => $new
        ));
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/update_password",name="agents_update_password")
     */
    public function updateUsersPassword(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('user');
            $user = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);

            if (!$user) {
                $this->addFlash('error', "Agent inexistant");
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
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
        /*return $this->redirectToRoute('agents_list');*/
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
     * @param Agent $agent
     * @param Request $request
     * @return Response
     * @Route("/delete/{agent}",name="agents_delete")
     */
    public function deleteAgent(Request $request, Agent $agent = null)
    {
        return $this->redirectToRoute('agents_list');
    }

    /**
     * @param Agent $agent
     * @param Request $request
     * @return Response
     * @Route("/orders/{agent}",name="agents_orders")
     */
    public function orderAgent(Request $request, Agent $agent = null)
    {
        return $this->redirectToRoute('agents_list');
    }
}