<?php

namespace AppBundle\Service\Back;


use AppBundle\Entity\Person;
use AppBundle\Entity\AgencePartner;
use AppBundle\Utilities\Helpers;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class UserService {

    private $em;

    private $router;

    private $passwordEncoder;

    private $mailer;

    private $container;

    private $fromEmail;

    private $userTypes;

    public function __construct(EntityManager $em,
                                Router $router,
                                UserPasswordEncoder  $encoder,
                                \Swift_Mailer $mailer,
                                ContainerInterface $container,
                                $fromEmail,
                                $userTypes){
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $encoder;
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
        $this->container = $container;
        $this->userTypes = $userTypes;
    }

    public function createUserForPerson(Person $person){

        $user = $person->getUser();

        foreach($this->userTypes as $roleType){
            if($roleType['key'] == $user->getType() ){
                $role = $roleType['role'];
                $type = $roleType['value'];
            }
        }


        $user->setRoles([$role])
            ->setSalt(Helpers::generateRandomString(30));

        $person->setEmail($user->getEmail());
        $password = Helpers::generateRandomString(8);
        $passwordEncoded = $this->passwordEncoder
            ->encodePassword($user,$password);
        $user->setPassword($passwordEncoded);

        try{

            $mailBody = $this->container
                ->get('templating')
                ->render("@App/mails/mail_inscription_user.html.twig",array(
                    'user' => $user,
                    'person' => $user->getPerson(),
                    'password' => $password,
                    'type' => $type
                ));
            $message = \Swift_Message::newInstance("[Navette de Vatry] Inscription ". ucfirst($type) );
            $message->setBody($mailBody,'text/html');
            $message->setTo($user->getEmail())
                ->setFrom($this->fromEmail);

            return $this->mailer->send($message);
        }catch (\Exception $e){
            return false;
        }

    }

    public function createUserForPartnerAgency(AgencePartner $agencePartner){

        $user = $agencePartner->getUser();

        $user->setSalt(Helpers::generateRandomString(30));

//        $agencePartner->setEmail($user->getEmail());
        $password = Helpers::generateRandomString(8);
        $passwordEncoded = $this->passwordEncoder
            ->encodePassword($user,$password);
        $user->setPassword($passwordEncoded);

        try{

            $mailBody = $this->container
                ->get('templating')
                ->render("@App/mails/mail_inscription_partner_agency.html.twig",array(
                    'user' => $user,
                    'partnerAgency' => $user->getAgencePartner(),
                    'password' => $password
                ));
            $message = \Swift_Message::newInstance("[Navette de Vatry] Inscription Agence Partenaire" );
            $message->setBody($mailBody,'text/html');
            $message->setTo($user->getEmail())
                ->setFrom($this->fromEmail);

            return $this->mailer->send($message);
        }catch (\Exception $e){
            return false;
        }

    }


}