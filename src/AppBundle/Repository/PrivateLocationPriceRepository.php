<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 18/04/2016
 * Time: 21:34
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Location;
use AppBundle\Entity\PrivateLocationPrice;
use Doctrine\ORM\EntityRepository;

class PrivateLocationPriceRepository extends EntityRepository {

    use ZipCodeTrait;
    use CountTrait;
    use LocationByZipCodeTrait;
    /**
     * @param Location $location
     * @param $qty
     * @return PrivateLocationPrice[]
     */
    public function findPriceByQty(Location $location,$qty){

        return $this->createQueryBuilder('l')
            ->where('l.maxCapacity >= :qty')
            ->andWhere('l.minCapacity <= :qty')
            ->andWhere('l.location = :location')
            ->setParameter('location',$location)
            ->setParameter('qty',intval($qty))
            ->getQuery()
            ->getResult();
    }

    public function search($data, $page = 0, $max = NULL, $getResult = true)
    {
        $qb = $this->_em->createQueryBuilder();

        $query = isset($data['query']) && $data['query']?$data['query']:null;

        $qb->select('m')
            ->from('AppBundle:PrivateLocationPrice', 'm')
        ;
        if ($query) {
            $qb->join('m.location','l')
                ->andWhere('m.price like :query OR m.id like :query OR l.name like :query OR m.minCapacity like :query OR m.maxCapacity like :query OR m.distance like :query OR m.zipCode like :query')
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