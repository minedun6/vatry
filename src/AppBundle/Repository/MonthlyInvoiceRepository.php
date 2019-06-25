<?php

namespace AppBundle\Repository;

/**
 * MonthlyInvoiceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MonthlyInvoiceRepository extends \Doctrine\ORM\EntityRepository
{
    public function getInvoiceInDate($year = null, $month = null, $partenrAgency = null){

        $qb = $this->createQueryBuilder('m')
            ->select("m")
            ->where('m.state = :state');

//        if($year && $month){
//            $qb->where('m.year = :year ')
//                ->andWhere('m.month = :month');
            if($partenrAgency){
                $qb->andWhere('m.partnerAgency = :partenrAgency')
                    ->setParameter('partenrAgency', $partenrAgency);
            }
//            $qb->setParameter('year', $year)
//                ->setParameter('month', $month);
//        }elseif($partenrAgency){
//            $qb->where('m.partnerAgency = :partenrAgency')
//                ->setParameter('partenrAgency', $partenrAgency);
//        }
        $qb->setParameter('state', 'wait_b2b');

        return  $qb->getQuery()->getResult();
    }

    public function getAllInvoiceInDate($year = null, $month = null, $partenrAgency = null){

        $qb = $this->createQueryBuilder('m')
            ->select("m");

        if($partenrAgency){
            $qb->andWhere('m.partnerAgency = :partenrAgency')
                ->setParameter('partenrAgency', $partenrAgency);
        }

        return  $qb->getQuery()->getResult();
    }

    public function getInvoiceNotInDate($year = null, $month = null, $partenrAgency = null){

        $qb = $this->createQueryBuilder('m')
            ->select("m")
            ->where('m.state = :state');

//        if($year && $month){
//            $qb->andWhere('m.year <= :year ')
//                ->andWhere('m.month < :month');
            if($partenrAgency){
                $qb->andWhere('m.partnerAgency = :partenrAgency')
                    ->setParameter('partenrAgency', $partenrAgency);
            }
//            $qb->setParameter('year', $year)
//                ->setParameter('month', $month);
//        }elseif($partenrAgency){
//            $qb->where('m.partnerAgency = :partenrAgency')
//                ->setParameter('partenrAgency', $partenrAgency);
//        }
        $qb->setParameter('state', 'paid_b2b');

        return  $qb->getQuery()->getResult();
    }
}