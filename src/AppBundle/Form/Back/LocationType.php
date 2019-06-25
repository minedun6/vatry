<?php

namespace AppBundle\Form\Back;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LocationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,array('required' => true,'label' => 'Nom'))
            ->add('zipCode',TextType::class,array('required' => true,'label' => 'ZIP'))
            ->add('type',TextType::class,array('required' => true,'label' =>'Type' ))
            ->add('lat',TextType::class,array('required' => true,'label' => 'Latitude'))
            ->add('lng',TextType::class,array('required' => true,'label' => 'Longitude'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Location'
        ));
    }
}
