<?php

namespace AppBundle\Form\Back;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AgglomerationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('id',IntegerType::class,array('required' => true,'attr' => array('pattern' => '[0-9]+')))
            ->add('name',TextType::class,array('required' => true,'label' => 'Nom'))
            ->add('lat',NumberType::class,array('required' => true,'label' => 'Latitude','attr'=> array('step' => 'any','pattern' => '([-]?[0-9]+([,\.][0-9]+)?)')))
            ->add('lng',NumberType::class,array('required' => true,'label' => 'Longitude','attr'=> array('step' => 'any','pattern' => '([-]?[0-9]+([,\.][0-9]+)?)')))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Agglomeration'
        ));
    }
}
