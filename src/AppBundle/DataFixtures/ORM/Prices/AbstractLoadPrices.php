<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 22:00
 */

namespace AppBundle\DataFixtures\ORM\Prices;

use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\PrivateLocationPrice;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Location;
abstract class AbstractLoadPrices
    extends AbstractFixtureContainerAware
    implements OrderedFixtureInterface  {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        echo "LOAD ".$this->getName()." PRICES FIXTURES \n";
        $filename = $this->container->getParameter("import_dir")."/".$this->getFileName().".csv" ;
        $this->setLogFile($this->container->getParameter("log_import_dir")."/".$this->getName().".log");

        if (!file_exists($filename)){
            $this->log("ABORT: ".$this->getName()." file doesn't exist ".$filename);
            $this->logLine();
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

                // on cherche la location par  code et par nom
                $location = $em->getRepository("AppBundle:Location")->findOneBy(
                    array(
                        'zipCode' => str_pad ($line[0],5,'0',STR_PAD_LEFT),
                        'name' => $line[1]
                    )
                );
                // si on ne trouve pas on cherche par code uniquement
      /*          if (!$location){
                    $this->log("Location by zipCode ".$line[0]." & Name Not found ".$line[1]);

                    $locations = $em->getRepository("AppBundle:Location")->findBy(
                        array(
                            'zipCode' => $line[0]
                        )
                    );
                    if (count($locations)  == 1){
                        $location = $locations[0];
                    }else{
                        $location = null;
                    }
                }
*/
                // sinon on vérifie que le champs nom existe et on crée la location et on l'ajoute
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
                    $location->setType($location::TYPE_COMMUNE);
                    $em->persist($location);
                    $em->flush();
                    $this->log("Location -->".$line[1]." was added in the location table with the id: ".$location->getId());
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
                        ->setZipCode(str_pad ($line[0],5,'0',STR_PAD_LEFT))
                        ->setPrice(floatval($line[intval($key)]));
                    $em->persist(clone $locationPrice);
                }

                echo "Importing ".$this->getName()." ".$line[1]."\n";
                $em->flush();
                $em->clear();
            }

        }
    }

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return string
     */
    abstract public function getFileName();

}