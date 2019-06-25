<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 05/06/2016
 * Time: 18:10
 */

namespace AppBundle\Service\Validator\Front\DateAllerRetourConstraint;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;

/**
 * Class DateAllerConstraint
 * @package AppBundle\Service\Validator\Front\DateAllerRetourConstraint
 * @Annotation
 */
class DateAllerConstraint extends Constraint{

    public  $message = "La date de l'aller doit être inférieure à celle de retour";

    public function getTargets(){
        return self::CLASS_CONSTRAINT;
    }

}