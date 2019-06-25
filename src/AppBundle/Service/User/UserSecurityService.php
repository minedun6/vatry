<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 27/04/2016
 * Time: 21:32
 */

namespace AppBundle\Service\User;


use AppBundle\Entity\User;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserSecurityService {

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * @var TwigEngine
     */
    private $twig;

    public function __construct(\Swift_Mailer $mailer,
                                EncoderFactoryInterface $encoderFactory,
                                TwigEngine $twig){
        $this->mailer = $mailer;
        $this->encoderFactory = $encoderFactory;
        $this->twig = $twig;
    }

    public function encodeUserPassword(User $user,$password){

        $encoder = $this->encoderFactory->getEncoder($user);
        $encoderPassword = $encoder->encodePassword($password, $user->getSalt());

        return $encoderPassword;
    }

    public function sendUserPassword(User $user,$password){

        $body = $this->twig->render("@App/mails/registration_confirmation.html.twig",array(
            'user' => $user,
            'password' => $password
        ));

        try{

            $message = \Swift_Message::newInstance()
                ->setSubject("[Vatry] Inscription Avec succès")
                ->setTo([$user->getEmail()])
                ->setFrom("navette.vatry.support@navettevatry.fr")
                ->setBody($body,'text/html');

             return $this->mailer->send($message);
        }catch (\Exception $e){
            return false;
        }

    }

    public function sendUserNewPassword(User $user,$password){
        $body = $this->twig->render("@App/mails/password_changed.html.twig",array(
            'user' => $user,
            'password' => $password
        ));

        try{

            $message = \Swift_Message::newInstance()
                ->setSubject("[Vatry] Mot de passe réinitialisé avec succès ")
                ->setTo([$user->getEmail()])
                ->setFrom("navette.vatry.support@navettevatry.fr")
                ->setBody($body,'text/html');

            return $this->mailer->send($message);

        }catch (\Exception $e){
            return false;
        }
    }

}