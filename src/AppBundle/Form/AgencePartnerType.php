<?php

namespace AppBundle\Form;

use AppBundle\Entity\AgencePartner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class AgencePartnerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',UserType::class)
            ->add('email', EmailType::class, array(
                'required' => false
            ))
            ->add('tel', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('nbjours', IntegerType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('destination', TextareaType::class, array(
                'constraints' => array(
                    new NotNull()
                ),
                    'attr' => array('rows' => 10)
            ))
            ->add('observations', TextareaType::class, array(
                'required' => false,
                'attr' => array('rows' => 10)
            ))
            ->add('referent', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('posteRefernet', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('activity', ChoiceType::class, array(
                'choices'  => array(
                    null => 'Choisir une activité',
                    'receptive' => 'Réceptive',
                    'emissive' => 'Emissive',
                    'receptive emissive'=> 'Réceptive emissive'
                )
            ))
            ->add('commission', NumberType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('rc', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AgencePartner::class
        ));
    }
}
