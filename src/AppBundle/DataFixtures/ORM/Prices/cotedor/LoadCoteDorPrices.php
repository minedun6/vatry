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

class LoadCoteDorPrices extends AbstractLoadPrices {

    public function getName(){
        return 'load_cote_d_or';
    }

    public function getFileName(){
        return 'cote_dor_prices';
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 11;
    }
}