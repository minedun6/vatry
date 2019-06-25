<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 16/04/2016
 * Time: 17:14
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class LocationRepository extends EntityRepository {
    use CountTrait;
    public function getZipCodes($like = null){
        $qb = $this->createQueryBuilder('l')
            ->select('DISTINCT l.zipCode');

        if ($like){
            $qb = $qb->where('l.zipCode like :like ')
                ->setParameter('like',"%$like%");
        }

        return $qb->getQuery()->getScalarResult();
    }

    //To Do a revoir il faut pas extraire les location qui ont un zip code correspandant dans locaton mais
    //plutot dans la table Private prices
    public function getCommunesByZipCode($zipCode = null,$like = null,$type = null){
        $qb = $this->createQueryBuilder('l')
            ->select('l.id, l.name, l.zipCode')
            ->where('1=1');

        if ($zipCode){
            $qb = $qb->andWhere('l.zipCode = :zipCode ')
                ->setParameter('zipCode',$zipCode);
        }

        if ($like){
            $qb = $qb->andWhere('l.name like :like ')
                ->setParameter('like',"%$like%");
        }

        return $qb->getQuery()->getArrayResult();
    }

    public function search($data, $page = 0, $max = NULL, $getResult = true)
    {
        $qb = $this->_em->createQueryBuilder();

        $query = isset($data['query']) && $data['query']?$data['query']:null;

        $qb->select('m')
            ->from('AppBundle:Location', 'm')
        ;
        if ($query) {
            $qb->Where('m.id like :query OR m.name like :query OR m.type like :query OR m.lat like :query OR m.lng like :query OR m.zipCode like :query')
                ->setParameter('query', "%".$query."%");
        }
        if ($max) {
            $preparedQuery = $qb->getQuery()
                ->setMaxResults($max)
                ->setFirstResult($page * $max)
            ;
        } else {
            $preparedQuery = $qb->getQuery();

        }
        return $getResult?$preparedQuery->getResult():$preparedQuery;
    }

}