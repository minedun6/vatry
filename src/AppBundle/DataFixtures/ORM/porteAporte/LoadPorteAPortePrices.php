<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 18:12
 */

namespace AppBundle\DataFixtures\ORM\interVillePrices;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\Location;
use AppBundle\Entity\PorteAPortePrice;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPorteAPortePrices extends AbstractFixtureContainerAware  implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        echo "LOAD PorteAPorte FIXTURES \n";
        $filename = $this->container->getParameter("import_dir") . "/porteAporte/porteAporte_prices.csv";
        $this->setLogFile($this->container->getParameter("log_import_dir") . "/porteAporte_prices.log");
        if (!file_exists($filename)) {
            $this->log("ABORT: porteAporte file doesn't exist ");
            $this->logLine();
            return;
        }
        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);
        $file = fopen($filename, 'r');

        if ($file) {
            //headers
            $headers = fgetcsv($file, null, ';');
            echo("je commence l'extraction en bouvlant sur les autres lignes \n");
            while ($line = fgetcsv($file, null, ';')) {
                if ($line[1] != '') {
                    $location = $em->getRepository("AppBundle:Location")->findOneBy(
                        array(
                            'zipCode' => $line[0],
                            'name' => $line[1]
                        )
                    );

                    // on cherche l'aglommération
                    echo("je cherche l'aglomeration");
                    $aglommeration = $em->getRepository("AppBundle:Agglomeration")->find($line[12]);
                    if (!$aglommeration) {
                        $this->log("Aglomeration by id " . $line[12] . " Not found ");
                    }
                    if (!$location) {
                        $this->log("Location by zipCode " . $line[0] . " & Name Not found " . $line[1]);

                        $locations = $em->getRepository("AppBundle:Location")->findBy(
                            array(
                                'zipCode' => $line[0]
                            )
                        );
                        if (count($locations) == 1) {
                            $location = $locations[0];
                        } else {
                            $location = null;
                        }
                    }


                    if (!$location) {
                        // Pourquoi ne pas créer la location qui n'existe pas ?
                        $this->log("Location -->" . $line[1] . "<-- not found in locations table");
                        $this->logLine();

                        $this->log("Location -->" . $line[1] . "<-- not found in locations table location will be created");
                        $this->logLine();
                        // continue;
                        /*
                         * Dans le cas ou on ne trouve pas la location on l'insère
                         */
                        $location = new Location();
                        $location->setName($line[1]);
                        $location->setZipCode(str_pad($line[0], 5, '0', STR_PAD_LEFT));
                        $em->persist($location);
                        $em->flush();
                        $this->log("Location -->" . $line[1] . " was added in the location table with the id: " . $location->getId());

                    }

                    $porteAPortePrice = new PorteAPortePrice();

                    $porteAPortePrice->setLocation($location)
                        ->setZipCode($line[0])
                        ->setPrice(str_replace(',', '.', $line[2]))
                        ->setTwoadultsPrice(str_replace(',', '.', $line[3]))
                        ->setThreeadultsPrice(str_replace(',', '.', $line[4]))
                        ->setChildPrice(str_replace(',', '.', $line[5]))
                        ->setBabyPrice(str_replace(',', '.', $line[6]))
                        ->setAgencyprice(str_replace(',', '.', $line[7]))
                        ->setAgencytwoadultsPrice(str_replace(',', '.', $line[8]))
                        ->setAgencythreeadultsPrice(str_replace(',', '.', $line[9]))
                        ->setAgencychildPrice(str_replace(',', '.', $line[10]))
                        ->setAgencybabyPrice(str_replace(',', '.', $line[11]))
                        ->setAgglomeration($aglommeration);


                    $em->persist($porteAPortePrice);
                    echo "Importing " . $line[1] . "\n";
                    $em->flush();
                    $em->clear();


                }
            }
        }
    }

    public function getOrder()
    {
        return 15;
    }
}