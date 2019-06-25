<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;


//class UserRepository extends EntityRepository implements UserLoaderInterface
class UserRepository extends EntityRepository
{
//    public function loadUserByUsername($username)
//    {
//        return $this->createQueryBuilder('u')
////            ->where('u.username = :username OR u.email = :email')
//            ->where('u.email = :email')
//            ->andWhere('u.deletedAT is null')
//            ->setParameter('username', $username)
//            ->setParameter('email', $username)
//            ->getQuery()
//            ->getOneOrNullResult();
//    }

    public function getUsersByType($types = array())
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u');

        if ($types){
            $qb
                ->where('u.type IN (:types )')
                ->setParameter('types', $types);
        }

        return $qb->getQuery()->getResult();
    }

    public function getUserByRole($role = null)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->select('u');

        if ($role){
            $qb
                ->where('u.roles LIKE :roles ')
                ->setParameter('roles', '%"'.$role.'"%');
        }

        return $qb->getQuery()->getResult();
    }
}
