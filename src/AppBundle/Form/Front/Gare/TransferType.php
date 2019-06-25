<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 04/05/2016
 * Time: 20:01
 */

namespace AppBundle\Form\Front\Gare;


use AppBundle\Entity\Flight;
use AppBundle\Entity\Location;
use AppBundle\Entity\Transfer;
use AppBundle\Form\EntityToIDTransformer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TransferType extends AbstractType
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
            ->add('roundTrip')
            ->add('qtyChild', IntegerType::class)
            ->add('qtyBaby', IntegerType::class)
            ->add('location', EntityType::class, array(
                'class' => Location::class,
                'choice_label' => 'name',
                'choice_attr' => function (Location $l) {
                    return array('zipCode' => $l->getZipCode());
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.type = :gare ')
                        ->setParameter('gare', Location::TYPE_GARE);
                }
            ))
            ->add('location2', EntityType::class, array(
                'class' => Location::class,
                'choice_label' => 'name',
                'choice_attr' => function (Location $l) {
                    return array('zipCode' => $l->getZipCode());
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.type = :gare ')
                        ->setParameter('gare', Location::TYPE_GARE);
                }
            ))
            ->add('date', DateType::class, array(
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy'
            ))
            ->add('date2', DateType::class, array(
                'required' => false,
                'mapped' => false,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'constraints' => array(
                    new Callback(array(
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            if ($value == null){
                                return ;
                            }

                            $data = $context->getRoot();
                            $date1 = $data['date']->getData();

                            if ($date1 == null){
                                return ;
                            }

                            if ($date1->format('Y-m-d') >= $value->format('Y-m-d') ){
                                $context->buildViolation("Date 1 doit etre inférieure à date 2")->addViolation();
                            }

                        }
                    ))
                )
            ))
            ->add('flight', HiddenType::class)
            ->add('flight2', HiddenType::class);


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