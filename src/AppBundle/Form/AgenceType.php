<?php

namespace AppBundle\Form;

use AppBundle\Entity\Agence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class AgenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('source', ChoiceType::class, array(
                'constraints' => array(
                    new NotNull()
                ),
                'choices'  => array(
                    null => 'Choisir un source',
                    'APV' => 'APV',
                    'NDV' => 'NDV'
                )
            ))
            ->add('reseau', TextType::class, array(
                    'required' => false
                )
            )
            ->add('adresse', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('adresse2', TextType::class, array(
                'required' => false
            ))
            ->add('cp', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('ville', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('email', EmailType::class)
            ->add('tel', TextType::class, array(
                'constraints' => array(
                    new NotNull()
                )
            ))
            ->add('tel', TextType::class, array(
                'required' => false
            ))
            ->add('fax', TextType::class, array(
                'required' => false
            ))
            ->add('web', TextType::class, array(
                'required' => false
            ))
//            ->add('web', UrlType::class, array(
//                'required' => false
//            ))
            ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Agence::class
        ));
    }
}
