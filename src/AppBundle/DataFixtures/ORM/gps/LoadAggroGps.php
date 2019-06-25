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

class LoadAggroGps extends AbstractFixtureContainerAware  {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {



        echo "LOAD GPS Aggro FIXTURES \n";
        $gpsVilleFile = $this->container->getParameter('data_dir')."/import/aggro_gps.csv";

        if (!file_exists($gpsVilleFile)){
            echo "ABORT: ALOAD GPS AGGRO FIXTURES ".$gpsVilleFile."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($gpsVilleFile,'r');

        if ($file){
            //headers
            fgetcsv($file);

            while($line = fgetcsv($file,null,';')){

                $location = $em->getRepository("AppBundle:Agglomeration")->findOneBy(array(
                    'name' => $line[1]
                ));

                if ($location){

                    $gpsData = explode(',',$line[2]);
                    $lat = trim($gpsData[0]);
                    $lng = trim($gpsData[1]);

                    $location->setLat($lat);
                    $location->setLng($lng);

                    $em->flush();


                }else{
                    echo "Agglo not found ".$line[2]."\n";
                }

            }

        }

    }

    public function getOrder()
    {
        return 20;
    }
}