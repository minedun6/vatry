<?php

namespace AppBundle\Controller\RelayCustomer;

use AppBundle\Entity\Person;
use AppBundle\Entity\RelayCustomerDetail;
use AppBundle\Entity\Transfer;
use DateInterval;
use DatePeriod;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use AppBundle\Form\AgentType;
use AppBundle\Utilities\Helpers;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class RelayCustomerController
 * @package AppBundle\Controller\RelayCustomer
 * @Route("/client-relais")
 */
class RelayCustomerController extends Controller
{
    /**
     * @Route("/index", name="relay_customer_list")
     */
    public function index()
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        if (in_array('ROLE_RELAY_CUSTOMER', $this->getUser()->getRoles()))
            return $this->redirectToRoute('relay_customer_transfers');
        $clients = $this->getDoctrine()
            ->getRepository("AppBundle:User")
            ->findByType('relayCustomer');

        return $this->render("AppBundle:RelayCustomer:index.html.twig", array(
            'clients' => $clients,
            'parent' => $parent
        ));
    }

    /**
     * @param Person $agent
     * @param Request $request
     * @return Response
     * @Route("/add/{id}", name="relay_customer_add", defaults={"id" = ""})
     */
    public function create(Request $request, Person $relay_customer = null)
    {

        $parent = $this->get('common.back.service')->getMenuExtends();
        $em = $this->getDoctrine()->getManager();
        $new = false;
        if ($relay_customer == null) {
            $relay_customer = new Person();
            $user = new User();
            $user->setType('relayCustomer');
            $relay_customer->setUser($user);
            $new = true;
        }

        $form = $this->createForm(AgentType::class, $relay_customer);
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($new) {
                    if ($this->__checkUserByEmail($user->getEmail())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render("AppBundle:RelayCustomer:add_edit.html.twig", array(
                            'form' => $form->createView(),
                            'parent' => $parent,
                            'new' => $new
                        ));
                    }

                    $sended = $this->get('users.service')
                        ->createUserForPerson($relay_customer);

                    if (!$user->getPerson()) $user->setPerson($relay_customer);
                    $em->persist($relay_customer);
                    $em->persist($user);

                    $details = new RelayCustomerDetail();
                    $details->setUser($user);
                    $details->setCreatedBy($this->getUser());
                    $details->setJob($request->get('job'));
                    $details->setType($request->get('type'));
                    $details->setCorporateName($request->get('corporateName'));
                    $em->persist($details);
                    if (!$sended) {
                        $this->addFlash('error', "Echec lors de l'envoi du mail au client");
                    }
                } else {
                    if ($this->__checkUpdateUserByEmail($relay_customer->getUser()->getEmail(), $relay_customer->getUser()->getId())) {
                        $this->addFlash('error', "Un compte avec cette adresse mail existe !");

                        return $this->render("AppBundle:RelayCustomer:add_edit.html.twig", array(
                            'form' => $form->createView(),
                            'parent' => $parent,
                            'new' => $new
                        ));
                    }

                    if ($relay_customer->getUser() != null) {
                        $details = $em->getRepository('AppBundle:RelayCustomerDetail')->findOneBy(
                            array('user' => $relay_customer->getUser())
                        );

                        if ($details == null) $details = new RelayCustomerDetail();
                        $details->setUser($relay_customer->getUser());
                        $details->setCreatedBy($this->getUser());
                        $details->setJob($request->get('job'));
                        $details->setType($request->get('type'));
                        $details->setCorporateName($request->get('corporateName'));
                        $em->persist($details);
                    }
                }

                $em->flush();

                if ($new) {
                    $this->addFlash('success', 'Client Relais ajouté avec succès');
                } else {
                    $this->addFlash('success', 'Client Relais modifié avec succès');
                }
                return $this->redirectToRoute('relay_customer_list');
            }
        }
        if ($relay_customer->getUser() != null) {
            $details = $em->getRepository('AppBundle:RelayCustomerDetail')->findOneBy(
                array('user' => $relay_customer->getUser())
            );
            if ($details != null)
                return $this->render("AppBundle:RelayCustomer:add_edit.html.twig", array(
                    'form' => $form->createView(),
                    'new' => $new,
                    'details' => $details,
                    'parent' => $parent
                ));
        }

        return $this->render("AppBundle:RelayCustomer:add_edit.html.twig", array(
            'form' => $form->createView(),
            'parent' => $parent,
            'new' => $new
        ));

    }

    /**
     * @Route("/details/{id}", name="relay_customer_details")
     */
    public function showDetailsAction()
    {

    }

    /**
     * @Route("/delete/{id}", name="relay_customer_delete")
     */
    public function delete()
    {

    }

    private function __checkUserByEmail($email)
    {
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneByEmail($email);
        if ($user) return true;
        else return false;
    }

    private function __checkUpdateUserByEmail($email, $id)
    {
        $user = $this->getDoctrine()->getRepository("AppBundle:User")->findOneByEmail($email);
        $userToUpdate = $this->getDoctrine()->getRepository("AppBundle:User")->findOneById($id);

        if ($user) {
            if ($user == $userToUpdate) return false;

            return true;
        } else return false;
    }


    /**
     * @Route("/transfers",name="relay_customer_transfers")
     *
     */
    public function transfersAction()
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em = $this->getDoctrine()->getRepository('AppBundle:Transfer');
        $user = $this->getUser();
        $result = $em->createQueryBuilder('t')
            ->select('SUM(t.price) as volume')
            ->where('t.createdBy = :user_id')
            ->andWhere('t.price IS NOT NULL')
            ->setParameter('user_id', $user->getId())
            ->andWhere('t.status = :status')
            ->setParameter('status', Transfer::STATUS_PAID)
            ->getQuery()
            ->getResult();

        $volume = isset($result[0]['volume']) ? number_format(doubleval($result[0]['volume']), '2', '.', '') : 0;
        $bonus = $user->getRelayCustomerDetail() ? number_format($user->getRelayCustomerDetail()->getBonus(), '2', '.', '') : 0;
        $types = array(Transfer::STATUS_PAID, Transfer::STATUS_PAID_RELAY, Transfer::STATUS_CANCEL, Transfer::STATUS_PAID_PENDING);
        $transfers = $em->findBy(
            array('createdBy' => $user->getId(), 'status' => $types),
            array('createdAt' => 'desc'),
            null,
            null);

        return $this->render("@App/RelayCustomer/Transfers/my_transfers.html.twig", array(
            'transfers' => $transfers,
            'volume' => $volume,
            'parent' => $parent,
            'bonus' => $bonus
        ));
    }

    /**
     * @Route("/transfer-details/{transfer}",name="relay_customer_transfer_details")
     */
    public function transferDetailsAction(Request $request, Transfer $transfer)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render("@App/RelayCustomer/Transfers/transfer_details.html.twig", array(
            'transfer' => $transfer,
            'parent' => $parent
        ));
    }


    /**
     * @Route("/transfers/{id}",name="relay_customer_get_transfers")
     *
     */
    public function transfersRelayAction(User $user = null)
    {
        $current_user = $this->getUser();
        if (in_array('ROLE_RELAY_CUSTOMER', $current_user->getRoles()) && $current_user->getId() != $user->getId()) {
            return $this->redirectToRoute('homepage');
        }
        $em = $this->getDoctrine()->getRepository('AppBundle:Transfer');
        $result = $em->createQueryBuilder('t')
            ->select('SUM(t.price) as volume')
            ->where('t.createdBy = :user_id')
            ->andWhere('t.price IS NOT NULL')
            ->setParameter('user_id', $user->getId())
            ->andWhere('t.status = :status')
            ->setParameter('status', Transfer::STATUS_PAID)
            ->getQuery()
            ->getResult();

        $volume = isset($result[0]['volume']) ? number_format(doubleval($result[0]['volume']), '2', '.', '') : 0;
        $bonus = $user->getRelayCustomerDetail() ? number_format($user->getRelayCustomerDetail()->getBonus(), '2', '.', '') : 0;
        $types = array(Transfer::STATUS_PAID, Transfer::STATUS_PAID_RELAY, Transfer::STATUS_CANCEL, Transfer::STATUS_PAID_PENDING);
        $transfers = $em->findBy(
            array('createdBy' => $user->getId(), 'status' => $types),
            array('createdAt' => 'desc'),
            null,
            null);
        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render("@App/RelayCustomer/Transfers/transfers.html.twig", array(
            'transfers' => $transfers,
            'parent' => $parent,
            'volume' => $volume,
            'bonus' => $bonus
        ));
    }

    /**
     * @Route("/commissions", name="relay_customer_commissions")
     * @param Request $request
     * @return string
     * @Method("GET")
     */
    public function getRelayCustomerCommissionsAction(Request $request)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em = $this->getDoctrine()->getRepository('AppBundle:Transfer');
        $user = $this->getUser();
        $month = $request->get('month') ? $request->get('month') : null;
        $year = $request->get('year') ? $request->get('year') : null;
        if (isset($month) && isset($year)) {
            $start_date = (new\DateTime())->setDate($year, $month, 01)->setTime(0, 0, 0);
            $end_date = (new \DateTime())->setDate($year, $month, 31)->setTime(23, 59, 59);
        } else {
            $start_date = (new \DateTime())->setDate(date('Y'), date('m'), 1)->setTime(0, 0, 0);
            $end_date = (new \DateTime())->setDate(date('Y'), date('m'), $this->calDaysInMonth(CAL_GREGORIAN, date('m'), date('Y')))->setTime(23, 59, 59);
        }


        $mesCommissions = $em->transfertParPeriode($start_date, $end_date, $user);
        $total = 0;
        foreach ($mesCommissions as $commission) {
            $total += $commission->getPrice();
        }
        return $this->render('@App/RelayCustomer/Transfers/commissions.html.twig', array(
            'com_date' => $start_date,
            'parent' => $parent,
            'commissions' => $mesCommissions,
            'total' => $total
        ));
    }

    /**
     * @Route("/commissions/monthly", name="relay_customer_monthly_commissions")
     * @param Request $request
     * @return string
     * @Method("GET")
     */
    public function getRelayCustomerCommissions(Request $request)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em = $this->getDoctrine()->getRepository('AppBundle:MonthlyCommission');
        $user = $this->getUser();
        $lastCommission = $em->getLastMonthlyCommission();

        $start = $lastCommission->getYear() . '-' . $lastCommission->getMonth() . '-01';
        $end = date('Y-m-d');

        $dates = $this->getMonthsInRange($start, $end);

        $start_date = (new \DateTime())->setDate($dates[0]['year'], $dates[0]['month'], 1)->setTime(0, 0, 0);;
        $end_date = (new \DateTime())->setDate(date('Y'), date('m'), cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')))->setTime(23, 59, 0);
        $commissions = $this->getDoctrine()->getRepository('AppBundle:Transfer')->transfertParPeriode($start_date, $end_date, $user);

        return $this->render('@App/pdf/factureRelayCustomerMonthly.html.twig');

    }

    /**
     * @param $startDate
     * @param $endDate
     * @return array
     */
    function getMonthsInRange($startDate, $endDate)
    {
        $months = array();
        while (strtotime($startDate) <= strtotime($endDate)) {
            $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)),);
            $startDate = date('d M Y', strtotime($startDate .
                '+ 1 month'));
        }

        return $months;
    }

    public function calDaysInMonth($calendar, $month, $year)
    {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }




    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/allTransfers/{type}/{affected}",name="rc_all_transfers")
     */
    public function listTransferActionRC(Request $request, $type=null, $affected=null)
    {
        $parent = $this->get('common.back.service')->getMenuExtends();
        $isLastMinute = $affected ? true : false;
        $user = $this->getUser();
        $transfers=null;

        $condition2 = array('createdBy' => $user,'affectedTo' => $user);

        if($affected)
            $condition2 = array('affectedTo' => $user);

        if($type=="paye"){
            $status=array(Transfer::STATUS_PAID_RC);


            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findRC($user, $status, $affected);
        }
        else if($type=="annule"){
            $status=array(Transfer::STATUS_CANCEL_RC);


            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findRC($user, $status, $affected);
        }
        else if ($type=="valid") {
            $status=array(Transfer::STATUS_VALID_RC );

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findRC($user, $status, $affected);
        }else if($type=="wait"){
            $status=array(Transfer::STATUS_WAIT_RC, Transfer::STATUS_OPEN_RC );

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findRC($user, $status, $affected);
        }else if($type=="open"){
            $status=array(Transfer::STATUS_OPEN_RC );

            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findRC($user, $status, $affected);
        }else if($type=="all"){
            $status=array(Transfer::STATUS_PAID_RC,Transfer::STATUS_CANCEL_RC,Transfer::STATUS_VALID_RC,Transfer::STATUS_WAIT_RC,Transfer::STATUS_OPEN_RC  );


            $transfers = $this->getDoctrine()->getRepository("AppBundle:Transfer")
                ->findRC($user, $status, $affected);

        }

        return $this->render("@App/RelayCustomer/Transfers/pending_transfers.html.twig", array(
            'transfers' => $transfers,
            'parent' => $parent,
            'isLastMinute' => $isLastMinute
        ));
    }







}
