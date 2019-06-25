<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Balance;
use AppBundle\Model\BalanceModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * BalanceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BalanceRepository extends EntityRepository
{

    /**
     * @param $date
     * get balances for a requested date
     * grouped by agent
     * @return Array
     */
    public function findBalancesByDate($date)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
            ->select(array(
                'SUM(b.cb) as cb',
                'SUM(b.cbVad) as cb_vad',
                'SUM(b.received) as received',
                'SUM(b.cash) as cash',
                'SUM(b.balance) as balance',
                'u.id as user_id'
            ))
            ->from('AppBundle:Balance', 'b')
            ->join('b.user', 'u')
            ->where('b.balanceDate LIKE :date')
            ->setParameter('date', '%' . $date->format('Y-m-d') . '%')
            ->groupBy('u.id')
            ->getQuery();
        $results = $query->getResult();
        $balances = new ArrayCollection();
        foreach ($results as $result) {
            if ($result['user_id'] != null) {
                $balance = new BalanceModel();
                $user = $em->getRepository('AppBundle:User')->find($result['user_id']);
                $balance->setUser($user);
                $balance->setCb(doubleval($result['cb']));
                $balance->setCbVad(doubleval($result['cb_vad']));
                $balance->setCash(doubleval($result['cash']));
                $balance->setReceived(doubleval($result['received']));
                $balance->setBalance(doubleval($result['balance']));
                $balances->add($balance);
            }
        }

        return $balances;
    }

    /**
     * @param $start_date
     * @param $end_date
     * get Balances between two requested dates
     * grouped by agent
     * @return Array
     */
    public function findBalancesByPeriod($start_date, $end_date)
    {
        $end_date ? $end_date->setTime(23, 59, 59) : null;
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
            ->select(array(
                'SUM(b.cb) as cb',
                'SUM(b.cbVad) as cb_vad',
                'SUM(b.received) as received',
                'SUM(b.cash) as cash',
                'SUM(b.balance) as balance',
                'u.id as user_id'
            ))
            ->from('AppBundle:Balance', 'b')
            ->join('b.user', 'u')
            ->where('b.balanceDate >= :start_date AND b.balanceDate <= :end_date ')
            ->setParameter('start_date', $start_date->format('Y-m-d H:i:s'))
            ->setParameter('end_date', $end_date->format('Y-m-d H:i:s'))
            ->groupBy('u.id')
            ->getQuery();
        $results = $query->getResult();

        $balances = new ArrayCollection();
        foreach ($results as $result) {
            if ($result['user_id'] != null) {
                $balance = new BalanceModel();
                $user = $em->getRepository('AppBundle:User')->find($result['user_id']);
                $balance->setUser($user);
                $balance->setCb(doubleval($result['cb']));
                $balance->setCbVad(doubleval($result['cb_vad']));
                $balance->setCash(doubleval($result['cash']));
                $balance->setReceived(doubleval($result['received']));
                $balance->setBalance(doubleval($result['balance']));
                $balances->add($balance);
            }
        }

        return $balances;
    }


    public function findBalancesByPeriodForAgent($start_date, $end_date, $user_id)
    {
        $end_date ? $end_date->setTime(23, 59, 59) : null;
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
            ->select(array(
                'SUM(b.cb) as cb',
                'SUM(b.cbVad) as cb_vad',
                'SUM(b.received) as received',
                'SUM(b.cash) as cash',
                'SUM(b.balance) as balance',
            ))
            ->from('AppBundle:Balance', 'b')
            ->join('b.user', 'u')
            ->where('b.balanceDate >= :start_date AND b.balanceDate <= :end_date ')
            ->andWhere('u.id = :user_id')
            ->setParameter('user_id', $user_id)
            ->setParameter('start_date', $start_date->format('Y-m-d H:i:s'))
            ->setParameter('end_date', $end_date->format('Y-m-d H:i:s'))
            ->getQuery();
        $results = $query->getResult();

        $balances = new ArrayCollection();
        foreach ($results as $result) {
            $balance = new BalanceModel();
            $balance->setCb(doubleval($result['cb']));
            $balance->setCbVad(doubleval($result['cb_vad']));
            $balance->setCash(doubleval($result['cash']));
            $balance->setReceived(doubleval($result['received']));
            $balance->setBalance(doubleval($result['balance']));
            $balances->add($balance);
        }

        return $balances;
    }

    public function findBalancesByDateForAgent($date, $user_id)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder()
            ->select(array(
                'SUM(b.cb) as cb',
                'SUM(b.cbVad) as cb_vad',
                'SUM(b.received) as received',
                'SUM(b.cash) as cash',
                'SUM(b.balance) as balance',
            ))
            ->from('AppBundle:Balance', 'b')
            ->join('b.user', 'u')
            ->where('b.balanceDate LIKE :date')
            ->andWhere('u.id = :user_id')
            ->setParameter('user_id', $user_id)
            ->setParameter('date', '%' . $date->format('Y-m-d') . '%')
            ->getQuery();

        $results = $query->getResult();
        $balances = new ArrayCollection();
        foreach ($results as $result) {
            $balance = new BalanceModel();
            $balance->setCb(doubleval($result['cb']));
            $balance->setCbVad(doubleval($result['cb_vad']));
            $balance->setCash(doubleval($result['cash']));
            $balance->setReceived(doubleval($result['received']));
            $balance->setBalance(doubleval($result['balance']));
            $balances->add($balance);
        }

        return $balances;
    }

}