<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 05/06/2016
 * Time: 18:12
 */

namespace AppBundle\Service\Validator\Front\DateAllerRetourConstraint;


use AppBundle\Entity\Transfer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DateAllerConstraintValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof Transfer){

            if ($value->getRoundTrip() && $value->getPickupDate() && $value->getPickupDate2()){

                if ($value->getPickupDate()->format('YmdHis') >= $value->getPickupDate2()->format('YmdHis')){
                    $this->context->buildViolation($constraint->message)
                        ->addViolation();
                }

            }

        }

    }
}