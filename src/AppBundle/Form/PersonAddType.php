<?php

namespace AppBundle\Form;

use AppBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonAddType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('email')
//            ->remove('town')
//            ->remove('zipCode')
//            ->remove('country')
//            ->remove('address')
            ->remove('user')
            ->add('user',UserAddType::class)
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Person::class
        ));
    }

    public function getParent()
    {
        return new PersonType();
    }
}
