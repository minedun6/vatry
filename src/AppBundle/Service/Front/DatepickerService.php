<?php

/**
 * Created by PhpStorm.
 * User: Ghassen
 * Date: 22/06/2016
 * Time: 09:36
 */
namespace AppBundle\Service\Front;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DatepickerService
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;
    protected $authorizationChecker;

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage    $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage,AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

    public function getDayStartParameter($transfert_type)
    {   $parameter=null;

        switch ($transfert_type) {
            case "private":
                $parameter=3;
                break;
            case "interVille":
                $parameter=3;
                break;
            case "porteAporte":
                $parameter=3;
                break;
            default:
                $parameter=3;
        }

        if($this->authorizationChecker->isGranted('ROLE_ADMIN'))
        {
            $parameter='-3d';
        }

        if($this->authorizationChecker->isGranted('ROLE_COMMERCIAL') || $this->authorizationChecker->isGranted('ROLE_SECRETARY') )
        {
            $parameter='-3d';
        }

        if($this->authorizationChecker->isGranted('ROLE_AGENT'))
        {
            $parameter='-3d';
        }

        return $parameter;
    }
}