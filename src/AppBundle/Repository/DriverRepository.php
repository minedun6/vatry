<?php

namespace AppBundle\Repository;

/**
 * DriverRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DriverRepository extends \Doctrine\ORM\EntityRepository
{


public function getActivatedDriver()
{
  $qb = $this->createQueryBuilder('g')
              ->where("g.status = true");

          return $qb;

}


}
