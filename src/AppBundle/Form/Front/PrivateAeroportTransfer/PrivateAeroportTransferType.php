<?php

namespace AppBundle\Form\Front\PrivateAeroportTransfer;

use AppBundle\Entity\Flight;
use AppBundle\Entity\Location;
use AppBundle\Entity\Transfer;
use AppBundle\Form\EntityToIDTransformer;
use AppBundle\Form\PersonType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivateAeroportTransferType extends AbstractType
{

    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('direction', ChoiceType::class, array(
                'choices' => array(
                    Transfer::TO_VATRY => "A déstination de l'aéroport Paris-Vatry",
                    Transfer::FROM_VATRY => "A partir de l'aéroport Paris-Vatry "
                )))
            ->add('qty', IntegerType::class)
            ->add('qtyChild', IntegerType::class)
            ->add('qtyBaby', IntegerType::class)
            ->add('roundTrip')
            ->add('address', TextareaType::class, array(
                'required' => false,
            ))
            ->add('address2', TextareaType::class, array(
                'required' => false,
            ))
            ->add('date', DateType::class, array(
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
            ))
            ->add('time', TextType::class, array(
                'mapped' => false
            ))
            ->add('time2', TextType::class, array(
                'mapped' => false
            ))
            ->add('externalFlightTime', TextType::class, array(
                'required' => false
            ))
            ->add('externalFlightTime2', TextType::class, array(
                'required' => false
            ))

            ->add('externalFlight', TextType::class, array(
                'required' => false
            ))
            ->add('externalFlight2', TextType::class, array(
                'required' => false
            ))
            ->add('date2', DateType::class, array(
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
            ))
            ->add('location', EntityType::class,array(
                'class'=> Location::class,
                'choice_label' => 'name',
                'placeholder' => "Veuillez séléctionner un airport",
                'query_builder'=> function(EntityRepository $er){
                    return $er->createQueryBuilder('a')->where('a.type = :airport')
                        ->setParameter('airport',Location::TYPE_AIRPORT)
                        ->andWhere('a.zipCode != :x')
                        ->setParameter('x','00000');
                }
            ))
            ->add('flight', HiddenType::class)
            ->add('flight2', HiddenType::class)
            ->add('passenger', PersonType::class);


        $builder
            ->get('flight')
            ->addModelTransformer(new EntityToIDTransformer($this->manager, Flight::class));
        $builder
            ->get('flight2')
            ->addModelTransformer(new EntityToIDTransformer($this->manager, Flight::class));


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
