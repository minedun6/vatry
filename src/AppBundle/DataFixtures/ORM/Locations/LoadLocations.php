<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 18:12
 */

namespace AppBundle\DataFixtures\ORM\Locations;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\Department;
use AppBundle\Entity\Location;
use AppBundle\Entity\Region;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadLocations extends AbstractFixtureContainerAware  implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        echo "LOAD LOCATIONS FIXTURES \n";
        $filename = $this->container->getParameter("import_dir")."/vatry_locations.csv" ;

        if (!file_exists($filename)){
            echo "ABORT: Locations file doesn't exist ".$filename."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($filename,'r');

        if ($file){
            //headers
            fgetcsv($file);

            while($line = fgetcsv($file,null,';')){
                $location = new Location();
                $location
                    ->setName($line[0])
                    ->setType($line[2])
                    ->setZipCode(str_pad ($line[1],5,'0',STR_PAD_LEFT));

                echo "Importing Location ".$line[0]."\n";
                $em->persist($location);
                $em->flush();
            }

        }
    }

    public function getOrder()
    {
        return 3;
    }
}