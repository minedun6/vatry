<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:21
 */

namespace AppBundle\Controller\Agent;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Driver;
use AppBundle\Entity\Transfer;
use AppBundle\Form\Back\DriverType;
use AppBundle\Form\AffectationDriverType;

/**
 * Class DriverController
 * @package AppBundle\Controller\Agent
 * @Route("/agent/gestion_chauffeurs")
 */
class DriverController extends Controller
{

  /**
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @Route("/allDrivers",name="agent_all_drivers")
   */
    public function indexAction()
    {

        $parent = $this->get('common.back.service')->getMenuExtends();
        $driver=new Driver();
      $form=$this->createForm(DriverType::class,$driver);


      $drivers = $this->getDoctrine()
          ->getRepository("AppBundle:Driver")
          ->findAll();

          return $this->render("@App/Agent/Drivers/listeDrivers.html.twig", array(
              'drivers' => $drivers,'form' => $form->createView(),
              'parent' => $parent
          ));

    }


    /**
    * @param Request $request
    * @return Response
     * @Route("/add_driver",name="add_driver")
     */
    public function newdriverAction(Request $request)
    {
      $em=$this->getDoctrine()->getManager();
      $driver=new Driver();
      $form=$this->createForm(DriverType::class,$driver);

      if ($request->getMethod() == 'POST') {
          $form->handleRequest($request);
           //$errors=$form->getErrorsAsString();
            if ($form->isValid()) {
              $driver->setStatus(true);
              $em->persist($driver);
              $em->flush();
            }
        }

         return $this->redirectToRoute('agent_all_drivers');

    }



    /**
    * @param Request $request
    * @return Response
     * @Route("/toggle_driver/{driver}",name="toggle_driver")
     */
    public function toggledriverAction(Request $request,Driver $driver)
    {

      $em = $this->getDoctrine()->getManager();
      //$driver = $em->getRepository('AppBundle:Driver')->find($driver->getId());
      $driver->getStatus()? $driver->setStatus(false):$driver->setStatus(true);

        //$em->persist($driver);
        $em->flush();

    return $this->redirectToRoute('agent_all_drivers');

    }


    /**
    * @param Request $request
    * @return Response
     * @Route("/edit_driver/{driver}",name="edit_driver")
     */
    public function editdriver(Request $request,Driver $driver)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
      $form = $this->createForm(DriverType::class, $driver);
      if ($request->getMethod() == 'GET') {
      return $this->render('@App/Agent/Drivers/modifierChauffeur.html.twig', array('form' => $form->createView(),'driver' =>$driver,
          'parent' => $parent));

      }

      else {

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

                  $em->persist($driver);
                  $em->flush();

        return $this->redirectToRoute('agent_all_drivers');

      }


    }


    /**
    * @param Request $request
    * @return Response
     * @Route("/affecter_chauffeur/{transfer}",name="affect_driver")
     */
    public function affecterdriver(Request $request, Transfer $transfer)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
      $drivers = $this->getDoctrine()->getRepository("AppBundle:Driver")->findAll();

      $form = $this->createForm(AffectationDriverType::class, $transfer);

      if ($request->getMethod() == 'GET') {
      return $this->render('@App/Agent/Drivers/affectationChauffeur.html.twig', array('form' => $form->createView(), 'transfer' => $transfer,'drivers' => $drivers,
          'parent' => $parent));
      }

      else {
          $form->handleRequest($request);
          $em = $this->getDoctrine()->getManager();

                    $em->persist($transfer);
                    $em->flush();


            $sended = $this->get('transfer.common.service')->sendDriverConfirmationVoucher($transfer);
            /*$this->log(" Mode prod : Paiement :sending voucher and facture immediat");
                    if (!$sended) {
                        $this->log("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                        $this->get('logger')->addCritical("SENDING VOUCHER ECHEC Ref Transfer " . $transfer->getReference());
                    }*/

          return $this->redirectToRoute('agent_all_transfers');

      }


    }


    private function log($msg, $level = 'INFO')
    {
        file_put_contents($this->container->getParameter('kernel.logs_dir') . "/payment_interface.log", $level . " LOG PAYMENT " . date('dmYHis') . $msg . "\n", FILE_APPEND);
    }







}
