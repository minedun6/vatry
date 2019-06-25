<?php

namespace AppBundle\Controller\Balance;

use AppBundle\Entity\Balance;
use AppBundle\Entity\BalanceHistory;
use AppBundle\Entity\Transfer;
use AppBundle\Model\BalanceModel;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class BalanceController
 * @package AppBundle\Controller\Balance
 * @Route("/balance")
 */
class BalanceController extends Controller
{
    /**
     * @Route("/index", name="balance_index")
     */
    public function indexAction(Request $request)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        $parent = $this->get('common.back.service')->getMenuExtends();
        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getRepository('AppBundle:Balance');
            $start_date = $request->get('start_date') != null ? new \DateTime($request->get('start_date')) : null;
            $end_date = $request->get('end_date') != null ? new \DateTime($request->get('end_date')) : null;
            if ($start_date != null && $end_date != null) {
                $balances = $em->findBalancesByPeriod($start_date, $end_date);
                return $this->render('AppBundle:Balance:index.html.twig', array(
                    'balances' => $balances,
                    'date' => $start_date,
                    'end_date' => $end_date,
                    'parent' => $parent
                ));
            } elseif ($start_date != null) {
                $balances = $em->findBalancesByDate($start_date);
                return $this->render('AppBundle:Balance:index.html.twig', array(
                    'balances' => $balances,
                    'date' => $start_date,
                    'parent' => $parent
                ));
            }
        }
        $date = new \DateTime('now');
        $em = $this->getDoctrine()->getManager();
        $balances = $em->getRepository("AppBundle:Balance")->findBalancesByDate($date);


        return $this->render('AppBundle:Balance:index.html.twig', array(
            'balances' => $balances,
            'date' => $date,
            'parent' => $parent
        ));
    }

    /**
     * @Route("/create", name="balance_create")
     */
    public function createAction(Request $request)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        $date = new \DateTime('now');
        if ($request->getMethod() == 'POST') {
            $date = $request->get('date') != null ? new \DateTime($request->get('date')) : '';
        }

        $results = $this->getDoctrine()->getRepository("AppBundle:Transfer")->getTransfersForBalance($date);
        $transfers = new ArrayCollection();
        foreach ($results as $result) {
            if ($result['user_id'] != null) {
                $em = $this->getDoctrine()->getRepository('AppBundle:User');
                $user = $em->find($result['user_id']);
                if ($user != null) {
                    $found = false;
                    $old_balance = null;
                    foreach ($transfers as $balance) {
                        if ($balance->getUser()->getId() == $user->getId()) {
                            $old_balance = $balance;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $new_balance = new BalanceModel();
                        $query = $this->getDoctrine()->getRepository('AppBundle:Balance')->createQueryBuilder('b')
                            ->where('b.balanceDate LIKE :date')
                            ->setParameter('date', '%' . $date->format('Y-m-d') . '%')
                            ->andWhere('b.user = :user_id')
                            ->setParameter('user_id', $user->getId())
                            ->getQuery();
                        $existed_balance = $query->getOneOrNullResult();
                        $existed_balance != null ? $new_balance->setReceived($existed_balance->getReceived()) : '';
                        $new_balance->setUser($user);
                        $new_balance->setCash($result['payment_type'] == Transfer::TYPE_CACHE ? doubleval($result['amount']) : 0);
                        $new_balance->setCbVad($result['payment_type'] == NULL ? doubleval($result['amount']) : 0);
                        $new_balance->setCb($result['payment_type'] == Transfer::TYPE_CREDIT_CARD ? doubleval($result['amount']) : 0);
                        $transfers->add($new_balance);
                    } else {
                        if ($result['payment_type'] == Transfer::TYPE_CREDIT_CARD) {
                            $old_balance->setCb($result['amount']);
                        } elseif ($result['payment_type'] == NULL) {
                            $old_balance->setCbVad($result['amount']);
                        } elseif ($result['payment_type'] == Transfer::TYPE_CACHE) {
                            $old_balance->setCash($result['amount']);
                        }
                        foreach ($transfers as $index => $balance) {
                            if ($balance->getUser()->getId() == $old_balance->getUser()->getId()) {
                                $transfers->remove($index);
                                $transfers->add($old_balance);
                            }
                        }
                    }
                }
            }
        }

        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render('AppBundle:Balance:create.html.twig', array(
            'transfers' => $transfers,
            'date' => $date,
            'parent' => $parent
        ));
    }

    /**
     * @Route("/store", name="balance_store")
     * Method({"POST"})
     */
    public function storeAction(Request $request)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        $users = $request->get('user_id');
        $balance_date = $request->get('balance_date') ? new \DateTime($request->get('balance_date')) : new \DateTime('now');
        $balance_id = $request->get('balance_id');
        $cb = $request->get('cb');
        $cb_vad = $request->get('cb_vad');
        $cash = $request->get('cash');
        $received = $request->get('received_cash');
        for ($i = 0; $i < sizeof($users); $i++) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->find($users[$i]);
            if ($user != null) {
                if (isset($balance_id[$i])) {
                    $query = $em->getRepository('AppBundle:Balance')->createQueryBuilder('b')
                        ->where('b.balanceDate LIKE :date')
                        ->setParameter('date', '%' . $balance_date->format('Y-m-d') . '%')
                        ->andWhere('b.user = :user_id')
                        ->setParameter('user_id', $user->getId())
                        ->getQuery();
                    $balance = $query->getOneOrNullResult();
                    if ($balance == null) {
                        $balance = new Balance();
                    }
                }
                $new_amount = doubleval($received[$i]);
                $old_amount = $balance->getReceived();

                $balance->setCash(doubleval($cash[$i]));
                $balance->setCb(doubleval($cb[$i]));
                $balance->setCbVad(doubleval($cb_vad[$i]));
                $balance->setUser($user);
                $balance->setReceived(doubleval($received[$i]));
                $balance->setCreatedAt(new \DateTime());
                $balance->setBalance($balance->getReceived() - $balance->getCash());
                $balance->setBalanceDate($balance_date);
                $em->persist($balance);
                if ($old_amount != $new_amount) {
                    $balance_history = new BalanceHistory();
                    $balance_history->setAmount($new_amount - $old_amount);
                    $balance_history->setBalance($balance);
                    $em->persist($balance_history);
                }
            }
        }
        $em->flush();
        return $this->redirectToRoute('balance_index');
    }

    /**
     * @Route("/details/{id}", name="balance_user_details")
     */
    public function balanceByUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        if ($user == null) {
            return $this->redirectToRoute('balance_index');
        }
        $balances = $em->getRepository('AppBundle:Balance')->findBy(array('user' => $user->getId()));

        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render('AppBundle:Balance:details.html.twig', array(
            'user' => $user,
            'balances' => $balances,
            'parent' => $parent
        ));
    }

    /**
     * @Route("/receipe", name="balance_receipe")
     */
    public function totalReceipeAction(Request $request)
    {
        if (!$this->__has_access() && !in_array('ROLE_ASSOCIATE', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('homepage');
        }
        $parent = $this->get('common.back.service')->getMenuExtends();
        $em = $this->getDoctrine()->getRepository('AppBundle:Transfer');
        if ($request->getMethod() == 'POST') {
            $start_date = $request->get('start_date');
            $start_date = isset($start_date) && $start_date != '' ? new \DateTime($start_date) : null;
            $end_date = $request->get('end_date');
            $end_date = isset($end_date) && $end_date != '' ? new \DateTime($end_date) : null;
            if ($start_date != null && $end_date != null) {
                $receipes = $em->getTotalReceipe($start_date, $end_date);
                $b2b_receipe = $em->getB2BReceipe($start_date, $end_date);
                return $this->render('AppBundle:Balance:receipe.html.twig', array(
                    'receipes' => $receipes,
                    'date' => $start_date,
                    'end_date' => $end_date,
                    'parent' => $parent,
                    'b2b_receipe' => $b2b_receipe

                ));
            } elseif ($start_date != null) {
                $receipes = $em->getTotalReceipe($start_date);
                $b2b_receipe = $em->getB2BReceipe($start_date);
                return $this->render('AppBundle:Balance:receipe.html.twig', array(
                    'receipes' => $receipes,
                    'date' => $start_date,
                    'parent' => $parent,
                    'b2b_receipe' => $b2b_receipe
                ));
            }
        }

        return $this->render('AppBundle:Balance:receipe.html.twig', array(
                'parent' => $parent
            )
        );
    }

    /**
     * @param Balance $balance
     * @Route("/history/{id}", name="balance_history")
     * @return Response
     */
    public function showBalanceDetails(Balance $balance)
    {
        if (!$this->__has_access()) {
            return $this->redirectToRoute('homepage');
        }
        if ($balance == null) {
            return $this->redirectToRoute('balance_index');
        }

        $parent = $this->get('common.back.service')->getMenuExtends();
        return $this->render('AppBundle:Balance:history.html.twig', array(
            'balance' => $balance,
            'parent' => $parent
        ));

    }

    private function __has_access()
    {
        $allowed_roles = ['ROLE_ADMIN', 'ROLE_SECRETARY'];
        $user = $this->getUser();
        if ($user != null) {
            if (!in_array($user->getRoles()[0], $allowed_roles)) {
                return false;
            }

            return true;
        }

        return false;
    }

}
