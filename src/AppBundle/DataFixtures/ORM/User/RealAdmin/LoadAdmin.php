<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 01/05/2016
 * Time: 20:13
 */

namespace AppBundle\DataFixtures\ORM\User\RealAdmin;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\User;
use AppBundle\Utilities\Helpers;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAdmin extends AbstractFixtureContainerAware {


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $admin = new User();

        $admin->setEmail("admin@navettevatry.fr")
            ->setSalt(Helpers::generateRandomString(20))
            ->setPassword($this->container->get('security.password_encoder')->encodePassword($admin,'@adnavvatry2016'))
            ->setRoles(array('ROLE_ADMIN'))
            ->setType('ADMIN');

        $manager->persist($admin);
        $manager->flush();
    }
}