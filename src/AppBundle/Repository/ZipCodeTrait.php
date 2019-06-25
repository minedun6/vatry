<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 25/04/2016
 * Time: 21:30
 */

namespace AppBundle\Repository;

Trait ZipCodeTrait {

    public function getZipCodes($like = null){
        $qb = $this->createQueryBuilder('l')
            ->select('DISTINCT l.zipCode');

        if ($like){
            $qb = $qb->where('l.zipCode like :like ')
                ->setParameter('like',"%$like%");
        }

        return $qb->getQuery()->getScalarResult();
    }

}