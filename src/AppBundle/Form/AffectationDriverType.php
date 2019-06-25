<?php

namespace AppBundle\Form;

use AppBundle\Entity\Transfer;
use AppBundle\Entity\Flight;
use AppBundle\Repository\DriverRepository;
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

class AffectationDriverType extends AbstractType
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
            ->add('driver', EntityType::class, array(
                'class' => 'AppBundle:Driver',
                'query_builder' => function (DriverRepository $dr) {
                  return $dr->getActivatedDriver();
                },
                'choice_label' => 'lastname'))

            ->add('pickupDate2', DateTimeType::class, array(
                'widget' => 'single_text',
                'format' => 'y-MM-dd HH:mm'
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
