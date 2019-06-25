<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 14/04/2016
 * Time: 21:43
 */

namespace AppBundle\Form\Front\InterVilleTransfer;


use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class InterVilleTransferFirstStepType extends InterVilleTransferType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('passenger');
    }

    public function getParent(){
        return InterVilleTransferType::class;
    }

}