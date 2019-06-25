<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 18:12
 */

namespace AppBundle\DataFixtures\ORM\Regions;


use AppBundle\DataFixtures\AbstractFixtureContainerAware;
use AppBundle\Entity\Agglomeration;
use AppBundle\Entity\Region;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadAgglomerations extends AbstractFixtureContainerAware implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        echo "LOAD Agglomeration FIXTURES \n";
        $filename = $this->container->getParameter("import_dir")."/agglomeration/agglomeration.csv" ;

        if (!file_exists($filename)){
            echo "ABORT: Agglomeration file doesn't exist ".$filename."\n";
            return ;
        }

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(NULL);

        $file = fopen($filename,'r');

        if ($file){
            //headers
            fgetcsv($file);

            while($line = fgetcsv($file,null,';')){
                $agglomeration = new Agglomeration();
                $agglomeration->setId($line[0]);
                $agglomeration->setName($line[1]);
                echo "Importing Agglomeration ".$line[1]."\n";
                $em->persist($agglomeration);
                $em->flush();
            }

        }
    }

    public function getOrder()
    {
        return 14;
    }
}