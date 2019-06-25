<?php

namespace AppBundle\Form\Back;
use AppBundle\Entity\PrivateLocationPrice;
use AppBundle\Entity\Location;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class PrivateLocationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('location', EntityType::class,array(
                'class'=> Location::class,
                'required' => true,
                'choice_label' => 'name',
                'placeholder' => "Veuillez séléctionner ",
                'query_builder'=> function(EntityRepository $er){
                    return $er->createQueryBuilder('a');
                }
            ))
            ->add('price',TextType::class,array('required' => true,'label' => 'Prix'))
            ->add('minCapacity',TextType::class,array('required' => true,'label' => 'Capacité minimale'))
            ->add('maxCapacity',TextType::class,array('required' => true,'label' => 'Capacité maximale'))
            ->add('zipCode',TextType::class,array('required' => true,'label' => 'ZIP'))
            ->add('distance',TextType::class,array('required' => true))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PrivateLocationPrice'
        ));
    }
}
