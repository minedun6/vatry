<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 18:12
 */

namespace AppBundle\DataFixtures\ORM\Agence;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\Agence;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadAgence extends AbstractFixtureContainerAware implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        echo "LOAD Agglomeration FIXTURES \n";
        $filename = $this->container->getParameter("import_dir")."/agence/agence.csv" ;

        if (!file_exists($filename)){
            echo "ABORT: Agence file doesn't exist ".$filename."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($filename,'r');

        if ($file){
            //headers
            fgetcsv($file);

            while($line = fgetcsv($file,null,';')){
                $agence = new Agence();
                $agence->setSource($line[0]);
                $agence->setNom($line[1]);
                $agence->setReseau($line[2]);
                $agence->setAdresse($line[3]);
                $agence->setAdresse2($line[4]);
                $agence->setCp($line[5]);
                $agence->setVille($line[6]);
                $agence->setEmail($line[7]);
                $agence->setTel($line[8]);
                $agence->setFax($line[9]);
                $agence->setWeb($line[10]);
                echo "Importing Agence ".$line[1]."\n";
                $em->persist($agence);
                $em->flush();
            }

        }
    }

    public function getOrder()
    {
        return 30;
    }
}