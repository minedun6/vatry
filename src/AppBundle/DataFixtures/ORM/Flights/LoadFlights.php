<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 16/04/2016
 * Time: 23:24
 */
namespace AppBundle\DataFixtures\ORM\Flights;

use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\Flight;
use AppBundle\Entity\Location;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFlights extends AbstractFixtureContainerAware  {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        echo "LOAD LOCATIONS FIXTURES \n";
        $filename = $this->container->getParameter("import_dir")."/flights/vols.csv" ;

        if (!file_exists($filename)){
            echo "ABORT: Flights file doesn't exist ".$filename."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($filename,'r');

        if ($file){
            //headers
            fgetcsv($file);

            while($line = fgetcsv($file,null,';')){
                $flight = new Flight();

                // on met la date dans le format correct
                $maDateDecomposee  = explode("/", $line[0]);
                $maDate=$maDateDecomposee[2].'/'.$maDateDecomposee[1].'/'.$maDateDecomposee[0].' '.$line[4];

                $flight->setNum($line[1])
                    ->setFromLocation($line[2])
                    ->setToLocation($line[3])
                    ->setTime(date_create_from_format('Y/m/d H:i',$maDate));


                echo "Importing Location ".$line[2]." et vol ".$line[1]." \n";
                $em->persist($flight);
                $em->flush();
            }

        }

    }

}