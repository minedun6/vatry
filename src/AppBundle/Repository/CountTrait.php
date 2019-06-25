<?php
/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 23/06/2016
 * Time: 11:55
 */

namespace AppBundle\Repository;


trait CountTrait
{
    public function getCount(){
        return $this->createQueryBuilder('l')
                    ->select('count(l.id)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }
}