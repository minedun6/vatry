<?php

namespace AppBundle\Form\Back;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FlightType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('num',TextType::class,array('required' => true,'label' => 'Vol n°'))
            ->add('fromLocation',TextType::class,array('required' => true,'label' => 'Départ'))
            ->add('toLocation',TextType::class,array('required' => true,'label' => 'Arrivée'))
            ->add('time', DateTimeType::class,array('required' => true,'label' => 'Date/heure'))
            ->add('country',TextType::class,array('required' => true,'label' => 'Pays'))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Flight'
        ));
    }
}
