<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 18:12
 */

namespace AppBundle\DataFixtures\ORM\Regions;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\Location;
use AppBundle\Entity\Region;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAirports extends AbstractFixtureContainerAware implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        echo "LOAD AIRPORTS FIXTURES \n";
        $filename = $this->container->getParameter("import_dir")."/vatry_airports.csv" ;

        if (!file_exists($filename)){
            echo "ABORT: Airports file doesn't exist ".$filename."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($filename,'r');

        if ($file){
            //headers
            fgetcsv($file);

            while($line = fgetcsv($file,null,';')){
                $airport = new Location();
                $airport
                    ->setType(Location::TYPE_AIRPORT)
                    ->setId($line[0])
                    ->setZipCode($line[2])
                    ->setName($line[1]);

                echo "Importing AIRPORT ".$line[1]."\n";
                $em->persist($airport);
                $em->flush();
            }

        }
    }

    public function getOrder()
    {
        return 4;
    }
}