<?php
/**
 * Created by PhpStorm.
 * User: aymen
 * Date: 27/04/2016
 * Time: 17:17
 */

namespace AppBundle\Repository;

Trait LocationByZipCodeTrait {

    public function getLocationByZipCodes($zipCode = null){
        $results = $this->findByZipCode($zipCode);
        return $results;
    }

}