<?php

namespace AppBundle\Form\Back;
use AppBundle\Entity\Agglomeration;
use AppBundle\Entity\Location;
use AppBundle\Form\EntityToIDTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PorteAPorteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', EntityType::class,array(
                'required' => true,
                'class'=> Location::class,
                'choice_label' => 'name',
                'placeholder' => "Veuillez séléctionner ",
                'query_builder'=> function(EntityRepository $er){
                    return $er->createQueryBuilder('a');
                }
            ))
            ->add('agglomeration', EntityType::class,array(
                'class'=> Agglomeration::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => "Veuillez séléctionner ",
                'query_builder'=> function(EntityRepository $er){
                    return $er->createQueryBuilder('a');
                }
            ))
            ->add('zipCode',TextType::class,array('required' => true,'label' => 'ZIP'))
            ->add('price',TextType::class,array('required' => true,'label' => 'Prix'))
            ->add('twoadultsPrice',TextType::class,array('required' => true,'label' => 'Prix 2 adultes'))
            ->add('threeadultsPrice',TextType::class,array('required' => true,'label' => 'Prix 3 adultes'))
            ->add('childPrice',TextType::class,array('required' => true,'label' => 'Prix 1 enfant'))
            ->add('babyPrice',TextType::class,array('required' => true,'label' => 'Prix 1 bébé'))
            ->add('agencyprice',TextType::class,array('required' => true,'label' => 'Prix agence'))
            ->add('agencytwoadultsPrice',TextType::class,array('required' => true,'label' => 'Prix agence 2 adultes'))
            ->add('agencythreeadultsPrice',TextType::class,array('required' => true,'label' => 'Prix agence 3 adultes'))
            ->add('agencychildPrice',TextType::class,array('required' => true,'label' => 'Prix agence 1 enfant'))
            ->add('agencybabyPrice',TextType::class,array('required' => true,'label' => 'Prix agence 1 bébé'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PorteAPortePrice'
        ));
    }
}
