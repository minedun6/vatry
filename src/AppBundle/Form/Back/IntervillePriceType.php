<?php

namespace AppBundle\Form\Back;
use AppBundle\Entity\City;
use AppBundle\Entity\Location;
use AppBundle\Repository\CityRepository;
use AppBundle\Form\EntityToIDTransformer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
class IntervillePriceType extends AbstractType
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
                'label' => 'Emplacement',
                'placeholder' => "Veuillez séléctionner ",
                'query_builder'=> function(EntityRepository $er){
                    return $er->createQueryBuilder('a');
                }
            ))
            ->add('zipCode',TextType::class,array('required' => true,'label' => 'ZIP'))
            ->add('rdv',TextType::class,array('required' => true,'label' => 'RDV'))
            ->add('adultePrice',TextType::class,array('required' => true,'label' => 'Prix adulte'))
            ->add('twoadultsPrice',TextType::class,array('required' => true,'label' => 'Prix 2 adultes'))
            ->add('threeadultsPrice',TextType::class,array('required' => true,'label' => 'Prix 3 adultes'))
            ->add('childPrice',TextType::class,array('required' => true,'label' => 'Prix enfant'))
            ->add('babyPrice',TextType::class,array('required' => true,'label' => 'Prix bébé'))
            ->add('agencyadultePrice',TextType::class,array('required' => true,'label' => 'Prix agence adulte'))
            ->add('agencytwoadultsPrice',TextType::class,array('required' => true,'label' => 'Prix agence 2 adultes'))
            ->add('agencythreeadultsPrice',TextType::class,array('required' => true,'label' => 'Prix agence 3 adultes'))
            ->add('agencychildPrice',TextType::class,array('required' => true,'label' => 'Prix agence enfant'))
            ->add('agencybabyPrice',TextType::class,array('required' => true,'label' => 'Prix agence bébé'))
            ->add('duration',TextType::class,array('required' => true,'label' => 'Durée'))
            ->add('cities', EntityType::class,array(
                'required' => false,
                'class'=> City::class,
                'query_builder' => function (CityRepository $cr) {
                  return $cr->getActivatedCity();
                },
                'choice_label' => 'designation',
                'expanded'=> false,
                'multiple'=> true,
                'placeholder' => "Veuillez séléctionner ",
            ))
        ;
   }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\InterVillePrices'
        ));
    }
}
