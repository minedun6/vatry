<?php

/**
 * Created by PhpStorm.
 * User: Aymen
 * Date: 26/07/2016
 * Time: 15:58
 */
namespace AppBundle\Service\Back;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CommonService
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;
    protected $authorizationChecker;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    public function getMenuExtends()
    {
        $parent = null;
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $parent = "@App/Back/index.html.twig";
        } elseif ($this->authorizationChecker->isGranted('ROLE_COMMERCIAL') || $this->authorizationChecker->isGranted('ROLE_SECRETARY')) {
            $parent = '@App/AgentAdmin/index.html.twig';
        } elseif ($this->authorizationChecker->isGranted('ROLE_AGENT')) {
            $parent = '@App/AgentAdmin/index.html.twig';
        } elseif ($this->authorizationChecker->isGranted('ROLE_PARTNER_AGENCY')) {
            $parent = '@App/PartnerAgency/index.html.twig';
        } elseif ($this->authorizationChecker->isGranted('ROLE_CUSTOMER') || $this->authorizationChecker->isGranted('ROLE_RELAY_CUSTOMER')) {
            $parent = '@App/User/index.html.twig';
        } elseif ($this->authorizationChecker->isGranted('ROLE_PARTNER') || $this->authorizationChecker->isGranted('ROLE_ASSOCIATE')) {
            $parent = '@App/Partner/index.html.twig';
        }

        return $parent;
    }
}

