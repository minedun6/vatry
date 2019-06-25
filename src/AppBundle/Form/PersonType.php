<?php

namespace AppBundle\Form;

use AppBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class PersonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('name', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('lastname', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('address', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('tel', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('town', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('civility', ChoiceType::class, array(
                'choices' => array('M' => 'M', 'Mme' => 'Mme'),
                'constraints' => array(
                    new NotNull()
                )))
            ->add('zipCode', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('country', CountryType::class, array(
                'constraints' => array(
                    new NotNull()
                ),
                'preferred_choices' => array('FR')
            ));
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
}
