<?php

namespace AppBundle\Form;

use AppBundle\Entity\Transfer;
use AppBundle\Entity\Flight;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ModifType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('pickupDate', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'y-MM-dd HH:mm'
            ))
            ->add('pickupDate2', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'y-MM-dd HH:mm'
            ))
            ->add('roundTrip', 'checkbox')
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'open' => 'open',
                    'paid' => 'paid',
                    'cancel' => 'cancel',
                    'paid_canc' => 'paid_canc',
                    'paid_pend' => 'paid_pend',
                    'recouve' => 'recouve',
                    'relay_paid' => 'paiment Bonus relais'
                )))
            ->add('flight', EntityType::class, array(
                'class' => 'AppBundle:Flight',
                'choice_label' => 'num'))
            ->add('flight2', EntityType::class, array(
                'class' => 'AppBundle:Flight',
                'choice_label' => 'num'))
            ->add('passenger', PersonType::class)
            ->add('remarques')
//            ->add('type',ChoiceType::class,array(
//                'choices'  => array(
//                    'private_airport_town' => 'Privé Airport/Ville',
//                    'private_airport_airport' => 'Privé Airport/Airport',
//                    'interville_airporttown' => 'Interville Airport/Ville',
//                    'porteaporte_airporttown' => 'Porte à porte',
//                    'tranfer_gare' => 'Transfert gare'
//                )
//            ))
//            ->add('type','text',array(
//                'disabled'=>true
//            ))
//            ->add('payment', ModeTransfertType::class ,array(
//                'disabled'=>true
//            ))
            ->add('address',TextType::class,array(
                'required' => false
            ))
            ->add('location',EntityType::class, array(
                'class' => 'AppBundle:Location',
                'choice_label' => 'name'))
            ->add('address2',TextType::class,array(
                'required' => false
            ))
            ->add('location2',EntityType::class, array(
                'class' => 'AppBundle:Location',
                'choice_label' => 'name'))
            ->add('payment', ModePayementType::class, array(
                'required' => false
            ));

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Transfer::class
        ));
    }

}