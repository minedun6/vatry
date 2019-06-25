<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 30/04/2016
 * Time: 20:00
 */

namespace AppBundle\Form\User;


use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as PasswordFieldType ;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PasswordType extends AbstractType {

    /**
     * @var UserPasswordEncoder
     */
    private $userPasswordEncoder ;

    public function __construct(UserPasswordEncoder $userPasswordEncoder){
        $this->userPasswordEncoder = $userPasswordEncoder;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $options['user'];

        $builder
            ->add('old_password',PasswordFieldType::class,array(
                'constraints' => array(
                    new NotNull(),
                    new Callback(array(
                        'callback'=> function($value,ExecutionContextInterface $context) use ($user){
                            if ($value == null){
                                return ;
                            }

                            if (!$this->userPasswordEncoder->isPasswordValid($user,$value)){
                                $context->buildViolation('Mot de passe incorrect')->addViolation();
                            }
                        }
                    ))
                )
            ))
            ->add('new_password',PasswordFieldType::class,array(
                'constraints' => array(
                    new NotNull(),
                    new Length(array(
                        'min'=> 8
                    ))
                )
            ))
            ->add('confirmation_password',PasswordFieldType::class,array(
                'constraints' => array(
                    new NotNull(),
                    new Callback(array(
                        'callback'=> function($value,ExecutionContextInterface $context) use ($user){

                            if ($value == null){
                                return ;
                            }

                            $data = $context->getRoot()->getData();

                            if ($data['new_password'] == null){
                                return ;
                            }

                            if ($data['new_password'] != $value){
                                $context->buildViolation('Mots de passe non identiques')->addViolation();
                            }

                        }
                    ))
                )
            ));

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'user' => User::class
        ));
    }

}