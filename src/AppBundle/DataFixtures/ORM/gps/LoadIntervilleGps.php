<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 22/05/2016
 * Time: 10:33
 */

namespace AppBundle\DataFixtures\ORM\gps;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use Doctrine\Common\Persistence\ObjectManager;

class LoadIntervilleGps extends AbstractFixtureContainerAware  {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {



        echo "LOAD GPS VILLES FIXTURES \n";
        $gpsVilleFile = $this->container->getParameter('data_dir')."/import/villes_gps.csv";

        if (!file_exists($gpsVilleFile)){
            echo "ABORT: ALOAD GPS VILLES FIXTURES ".$gpsVilleFile."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($gpsVilleFile,'r');

        if ($file){
            //headers
            fgetcsv($file);

            while($line = fgetcsv($file,null,';')){

                $location = $em->getRepository("AppBundle:Location")->findOneBy(array(
                    'name' => $line[2],
                    'zipCode' => $line[1]
                ));

                if ($location){

                    $gpsData = explode(',',$line[3]);
                    $lat = trim($gpsData[0]);
                    $lng = trim($gpsData[1]);

                    $location->setLat($lat);
                    $location->setLng($lng);

                    $em->flush();


                }else{
                    echo "Location not found ".$line[2]." ".$line[1]."\n";
                }

            }

        }

    }

    public function getOrder()
    {
        return 21;
    }
}