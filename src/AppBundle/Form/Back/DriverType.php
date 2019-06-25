<?php

namespace AppBundle\Form\Back;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Driver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class DriverType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class)
            ->add('lastname',TextType::class)
            ->add('company',TextType::class)
            ->add('activity',TextType::class)
            ->add('phone',TextType::class)
            ->add('email',TextType::class)
            ->add('vehicule',TextType::class)
            ->add('vehiculeCapacity',TextType::class)
            ->add('vehiculeColor',TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Driver'
        ));
    }


    /**
    * {@inheritdoc}
    */
   public function getBlockPrefix()
   {
       return 'appbundle_driver';
   }
}
