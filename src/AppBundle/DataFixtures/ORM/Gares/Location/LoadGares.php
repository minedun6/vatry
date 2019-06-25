<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 08/05/2016
 * Time: 23:39
 */

namespace AppBundle\DataFixtures\ORM\Gares\Location;


use AppBundle\Entity\InterVillePrices;
use AppBundle\Entity\Location;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadGares extends AbstractFixture {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $gare1 = $manager->getRepository("AppBundle:Location")
            ->findOneBy(array('zipCode'=> Location::GARE_CHALONS));
        if (!$gare1){
            $gare1 = new Location();
            $gare1->setZipCode(Location::GARE_CHALONS)
                ->setType(Location::TYPE_GARE);
            $manager->persist($gare1);
        }
        $gare1->setName("Gare de Châlons-en-Champagne ");

        $gare2 = $manager->getRepository("AppBundle:Location")
            ->findOneBy(array('zipCode'=> Location::GARE_REIMS));
        if (!$gare2){
            $gare2 = new Location();
            $gare2->setZipCode(Location::GARE_REIMS)
                ->setType(Location::TYPE_GARE);
            $manager->persist($gare2);
        }
        $gare2->setName("Gare de Reims");

        $gare1Price = $manager->getRepository("AppBundle:InterVillePrices")->findOneBy(
            array('location'=> $gare1)
        );

        if (!$gare1Price){
            $gare1Price = new InterVillePrices();
            $gare1Price->setZipCode(Location::GARE_CHALONS)
                ->setLocation($gare1);
            $manager->persist($gare1Price);
        }
        $gare1Price->setAdultePrice(14)
            ->setBabyPrice(0)
            ->setChildPrice(14);


        $gare2Price = $manager->getRepository("AppBundle:InterVillePrices")->findOneBy(
            array('location'=> $gare2)
        );

        if (!$gare2Price){
            $gare2Price = new InterVillePrices();
            $gare2Price->setZipCode(Location::GARE_REIMS)
                ->setLocation($gare2);
            $manager->persist($gare2Price);
        }
        $gare2Price->setAdultePrice(20)
            ->setBabyPrice(0)
            ->setChildPrice(20);

        $manager->flush();
    }
}