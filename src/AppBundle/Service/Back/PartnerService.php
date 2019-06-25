<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 19/06/2016
 * Time: 12:49
 */

namespace AppBundle\Service\Back;


use AppBundle\Entity\Partner;
use AppBundle\Entity\SourceToken;
use AppBundle\Entity\User;
use AppBundle\Utilities\Helpers;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class PartnerService {

    private $em;

    private $router;

    private $passwordEncoder;

    private $mailer;

    private $container;

    private $fromEmail;

    public function __construct(EntityManager $em,
                                Router $router,
                                UserPasswordEncoder  $encoder,
                                \Swift_Mailer $mailer,
                                ContainerInterface $container,
                                $fromEmail){
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $encoder;
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->container = $container;
    }


    public function generateTokenForPartner(Partner $partner){

        $exist = true;
        $token = Helpers::generateRandomString();
        while ($exist){
            $sToken = $this->em->getRepository("AppBundle:SourceToken")->findOneBy(
                [
                    'token' => $token
                ]
            );
            if ($sToken == null){
                $exist = false;
            }else{
                $token = Helpers::generateRandomString();
            }
        }

        $sToken = new SourceToken();
        $sToken->setToken($token)
            ->setPartner($partner)
            ->setExpired(false);

        $this->em->persist($sToken);
        $this->em->flush();

        return $sToken;
    }

    public function getUrlForPartner(Partner $partner){

        $token = $this->em->getRepository("AppBundle:SourceToken")
            ->findOneBy([
                'partner' => $partner,
                'expired' => false
            ],[
                'createdAt' => 'DESC'
            ]);

        if (!$token){
            return null;
        }

        $url = $this->router->generate('homepage',[],true);
        $url = $url."?__st=".$token->getToken();
        return $url;
    }

    public function createUserForPartner(Partner $partner){
        $user = $partner->getUsers()[0];
        $user->setRoles(['ROLE_PARTNER'])
            ->setSalt(Helpers::generateRandomString(30))
            ->setType(User::TYPE_PARTNER);
        $password = Helpers::generateRandomString(8);
        $passwordEncoded = $this->passwordEncoder
            ->encodePassword($user,$password);
        $user->setPassword($passwordEncoded);

        try{

            $mailBody = $this->container
                ->get('templating')
                ->render("@App/mails/mail_inscription_partner.html.twig",array(
                    'user' => $user,
                    'partner' => $partner,
                    'password' => $password
                ));
            $message = \Swift_Message::newInstance("[Navette de Vatry] Inscription Partenaire");
            $message->setBody($mailBody,'text/html');
            $message->setTo($user->getEmail())
                ->setFrom($this->fromEmail);

            return $this->mailer->send($message);
        }catch (\Exception $e){
            return false;
        }

    }

}