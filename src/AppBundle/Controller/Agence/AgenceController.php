<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\Agence;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utilities\Helpers;
use AppBundle\Entity\Agence;
use AppBundle\Form\AgenceType;

/**
 * Class DefaultController
 * @package AppBundle\Controller\Back
 * @Route("/agence")
 */
class AgenceController extends Controller
{
    /**
     * @Route("/listeagence",name="liste_agence")
     */
    public function ListAgenceAction(Request $request)
    {
        if (in_array('ROLE_PARTNER', $this->getUser()->getRoles())) {
            $partner = $this->getUser()->getPartner();
            if (!$partner->getIsAirport()) {
                return $this->redirectToRoute('homepage');
            }
        }
        $agence = $this->getDoctrine()->getRepository("AppBundle:Agence")->findAll();
        $parent = $this->get('common.back.service')->getMenuExtends();

        return $this->render("@App/Back/Agence/listeAgence.html.twig", array(
            'agences' => $agence,
            'parent' => $parent
        ));


    }

    /**
     * @Route("/loadagencedata",name="agence_data")
     */
    public function getDataPl(Request $request)
    {
        $length = $request->get('length');
        $length = $length && ($length != -1) ? $length : 0;
        $start = $request->get('start');
        $start = $length ? ($start && ($start != -1) ? $start : 0) / $length : 0;
        $search = $request->get('search');
        $filters = [
            'query' => @$search['value']
        ];
        $agences = $this->getDoctrine()->getRepository('AppBundle:Agence')->search(
            $filters, $start, $length
        );
        $output = array(
            'data' => array(),
            'recordsFiltered' => count($this->getDoctrine()->getRepository('AppBundle:Agence')->search($filters, 0, false)),
            'recordsTotal' => count($this->getDoctrine()->getRepository('AppBundle:Agence')->search(array(), 0, false))
        );
        foreach ($agences as $agence) {
            $supprimerBTN = '';
            $togglebutton = '';
          


            if (!$agence->getAgencePartner()) {
                $partnerpath = $this->get('router')->generate('partner_agency_add', array('agency' => $agence->getId()));

                $colored = '';
                $pwdModify = '';
                $listeCommandes = '';
                $supprimerBTN = '<a href=' . $this->get("router")->generate("delete_agence", array("agence" => $agence->getId())) . ' title="Supprimer">
                   <span class="glyphicon glyphicon-trash"></span></a>';
            } else {
                $partnerpath = $this->get('router')
                    ->generate('partner_agency_add',
                        array('agency' => $agence->getId(),
                            'agencePartner' => $agence->getAgencePartner()->getId()));

               $partneractivatepath = $this->get('router')
                    ->generate('activate_agency',
                            array('agency' => $agence->getId(),
                              'agencePartner' => $agence->getAgencePartner()->getId()));

               $partnerdesactivatepath = $this->get('router')
                      ->generate('desactivate_agency',
                              array('agency' => $agence->getId(),
                                'agencePartner' => $agence->getAgencePartner()->getId()));

                $colored = 'green';
                $pwdModify = '<button data-toggle="tooltip" data-placement="top" title="Modifier mot de passe" valeur=' . $agence->getAgencePartner()->getUser()->getId() . ' onclick="updateP(' . $agence->getAgencePartner()->getUser()->getId() . ')"><span class="glyphicon glyphicon-lock"></span>
                        </button>';
//                $listeCommandes = $this->get("router")->generate("list_agency_com_transfers",array( "agency" => $agence->getId()));
                $listeCommandes = '<a data-toggle="tooltip" data-placement="top" title="Consulter les transferts" target="_blank"
                        href=' . $this->get("router")->generate("list_agency_com_transfers", array("agency" => $agence->getId())) . '>
                        <span class="glyphicon glyphicon-list"></span></a>';

                if($agence->getAgencePartner()->getStatus()== false )
                {
                $togglebutton= '<a data-toggle="tooltip" data-placement="top" title="Activer agence"
                    href=' . $partneractivatepath . '><span class="glyphicon glyphicon-hand-up"></span>
                 </a>';
                 }

                 else {
                   $togglebutton= '<a data-toggle="tooltip" data-placement="top" title="Desactiver agence"
                       href=' . $partnerdesactivatepath . '><span class="glyphicon glyphicon-hand-down"></span>
                    </a>';

                 }

            }
            $columns = [
                'agence' => $agence->getNom(),
                'reseau' => $agence->getReseau(),
                'adresse' => $agence->getAdresse(),
                'adresse2' => $agence->getAdresse2(),
                'cp' => $agence->getCp(),
                'ville' => $agence->getVille(),
                'email' => $agence->getEmail(),
                'tel' => $agence->getTel(),
                'fax' => $agence->getFax(),
                'web' => $agence->getWeb()
            ];


            if ($this->__has_full_access()) {
                $columns['action'] = '<td>
                    <a data-toggle="tooltip" data-placement="top" title="Modifier"
                       href=' . $this->get("router")->generate("agencies_add", array("agency" => $agence->getId())) . '><span
                                class="glyphicon glyphicon-edit"></span></a>

                    <a data-toggle="tooltip" data-placement="top" title="Agence partenaire"
                       href=' . $partnerpath . '><span class="glyphicon ' . $colored . ' glyphicon-user"></span>
                    </a>

                    <a href=' . $this->get("router")->generate("details_agency", array("agency" => $agence->getId())) . ' title="Détails"
                       target="_blank"> <span class="glyphicon glyphicon-search"></span> </a>


                    ' . $supprimerBTN . $togglebutton. $listeCommandes . $pwdModify . '</td>';
            } else {
                $columns['action'] = '<td>
                    <a data-toggle="tooltip" data-placement="top" title="Modifier"
                       href=' . $this->get("router")->generate("agence_add", array("agency" => $agence->getId())) . '><span
                                class="glyphicon glyphicon-edit"></span></a>
                    <a href=' . $this->get("router")->generate("agency_details", array("agency" => $agence->getId())) . ' title="Détails"
                       target="_blank"> <span class="glyphicon glyphicon-search"></span> </a></td>';
            }

            $output['data'][] = $columns;

        }
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @param Request $request
     * @param $partnerAgency
     * @return Response
     * @Route("/update_agency_password/{partnerAgency}",name="users_agence_update_password")
     */
    public function updateUsersPassword(Request $request, $partnerAgency = false)
    {
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('user');
            $user = $this->getDoctrine()->getRepository("AppBundle:User")->find($id);

            if (!$user) {
                $this->addFlash('error', "Agence Partenaire inexistante");
                $this->addFlash('error', "Echec lors de la modification du mot de passe de l'agence partenaire");
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
        return $this->redirectToRoute('liste_agence');
    }

    public function __has_full_access()
    {
        $user = $this->getUser();
        if ($user != null) {
            if (in_array('ROLE_PARTNER', $user->getRoles())) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * @param Agence $agency
     * @param Request $request
     * @return Response
     * @Route("/add/{agency}",name="agence_add")
     */
    public function newEditAgency(Request $request, Agence $agency = null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em = $this->getDoctrine()->getManager();

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
                            'new' => $new
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

    /**
     * @param Agence $agency
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/details/{agency}",name="agency_details")
     */
    public function detailsTransferAction(Agence $agency)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $render = "@App/AgentAdmin/Agencies/agency_details.html.twig";
        if ($this->isGranted('ROLE_ADMIN'))
            $render = "@App/Back/Agencies/agency_details.html.twig";

        return $this->render($render, array(
            'agency' => $agency,
            'parent' => $parent
        ));
    }

    /**
     * @param $agence
     * @Route("/{agence}", name="delete_agence")
     * @return RedirectResponse
     */
    public function deleteAgenceAction(Agence $agence)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($agence);
        $em->flush();

        return $this->redirectToRoute('liste_agence');
    }

}
