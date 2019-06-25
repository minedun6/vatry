<?php

namespace AppBundle\Form;

use AppBundle\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class AgentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('user')
            ->add('user',UserType::class);


            /*->add('type',ChoiceType::class,array(
             'mapped' => false,'required' =>true,'choices'
             => array('personne' => 'Personne physique', 'agence' => 'Agence de voyage ','association' => 'Association','commerce' => 'Commerce'),
           ))

            ->add('corporatename',TextType::class,array(
            'mapped' => false,'required' =>false));*/
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
        return new PersonAddType();
    }
}
