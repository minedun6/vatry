<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 20:54
 */

namespace AppBundle\DataFixtures\ORM\Prices;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\Location;
use AppBundle\Entity\PrivateLocationPrice;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAirportsPrices extends AbstractFixtureContainerAware implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        echo "LOAD AIRPORTS PRICES FIXTURES \n";
        $filename = $this->container->getParameter("import_dir")."/airports_prices.csv" ;
        $this->setLogFile($this->container->getParameter("log_import_dir")."/airports_prices.log");

        if (!file_exists($filename)){
            $this->log("ABORT: Airports file doesn't exist ".$filename);
            $this->logLine();
            echo "ABORT: Airports file doesn't exist ".$filename."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($filename,'r');

        if ($file){
            //headers
            $headers = fgetcsv($file,null,';');
            $grid = array_slice($headers,3,null,true);

            while($line = fgetcsv($file,null,';')){
                $location = $em->getRepository("AppBundle:Location")->findOneBy(
                    array(
                        'type' => Location::TYPE_AIRPORT,
                        'zipCode' => $line[0]
                    )
                );

                if (!$location){
                    $this->log("Location by zipCode ".$line[0]." & TYPE Not found ".$line[1]);

                    $locations = $em->getRepository("AppBundle:Location")->findBy(
                        array(
                            'zipCode' => $line[0]
                        )
                    );
                    if (count($locations)  == 1){
                        $location = $locations[0];
                    }else{
                        $location = null;
                        if (count($locations)>1){
                            $this->log("MORE THAN ONE LOCATION FOR ".$line[0]);
                        }
                    }
                }

                if (!$location && $line[1]!=''){
                    $this->log("Location -->".$line[1]."<-- not found in locations table");
                    $this->logLine();
                    //continue;
                    $this->log("Location -->".$line[1]."<-- not found in locations table location will be created");
                    $this->logLine();
                    // continue;
                    // Dans le cas ou on ne trouve pas la location on l'insère
                    $location=new Location();
                    $location->setName($line[1]);
                    $location->setZipCode(str_pad ($line[0],5,'0',STR_PAD_LEFT));
                    $location->setType(Location::TYPE_AIRPORT);
                    $em->persist($location);
                    $em->flush();
                    $this->log("Location Aeroport-->".$line[1]." was added in the location table with the id: ".$location->getId());

                }

                foreach($grid as $key => $value){
                    $capacities = explode('-',$value);
                    $min = intval($capacities[0]);
                    $max = intval($capacities[1]);
                    $locationPrice = new PrivateLocationPrice();
                    $locationPrice
                        ->setDistance(intval($line[2]))
                        ->setLocation($location)
                        ->setMinCapacity($min)
                        ->setMaxCapacity($max)
                        ->setPrice($line[intval($key)])
                        ->setZipCode(str_pad ($line[0],5,'0',STR_PAD_LEFT));
                    $em->persist(clone $locationPrice);
                }

                echo "Importing AIRPORT PRICE".$line[1]."\n";
                $em->flush();
                $em->clear();
            }

        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}