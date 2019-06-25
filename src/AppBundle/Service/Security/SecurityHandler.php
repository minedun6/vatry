<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 20/04/2016
 * Time: 21:47
 */

namespace AppBundle\Service\Security;


use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

    private $router;
    private $session;
    private $twig;
    private $authUtils;
    private $translator;


    /**
     * Constructor
     *
     * @author    Joe Sexton <joe@webtipblog.com>
     * @param    RouterInterface $router
     * @param    Session $session
     */
    public function __construct(RouterInterface $router,
                                Session $session,
                                TwigEngine $twigEngine,
                                AuthenticationUtils $authenticationUtils,
                                Translator $translator
    )
    {
        $this->router = $router;
        $this->session = $session;
        $this->twig = $twigEngine;
        $this->authUtils = $authenticationUtils;
        $this->translator = $translator;
    }

    /**
     * onAuthenticationSuccess
     *
     * @author    Joe Sexton <joe@webtipblog.com>
     * @param    Request $request
     * @param    TokenInterface $token
     * @return    Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // if AJAX login
        if ($request->isXmlHttpRequest()) {

            $array = array('success' => true); // data to return via JSON
            $response = new JsonResponse($array);
            return $response;

            // if form login
        } else {

            if ($this->session->get('_security.main.target_path')) {

                $url = $this->session->get('_security.main.target_path');

            } else {

                if (in_array('ROLE_ADMIN', $token->getUser()->getRoles())) {
                    $url = $this->router->generate('back_index');
                } elseif (in_array('ROLE_PARTNER', $token->getUser()->getRoles())) {
                    $url = $this->router->generate('partners_index');
                } else {
                    $url = $this->router->generate('homepage');
                }

            } // end if

            return new RedirectResponse($url);

        }
    }

    /**
     * onAuthenticationFailure
     *
     * @author    Joe Sexton <joe@webtipblog.com>
     * @param    Request $request
     * @param    AuthenticationException $exception
     * @return    Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // if AJAX login
        if ($request->isXmlHttpRequest()) {

            $array = array('success' => false, 'message' => $this->translator
                ->trans($exception->getMessageKey(), [], 'security')); // data to return via JSON
            $response = new JsonResponse($array);
            return $response;

            // if form login
        } else {

            // last username entered by the user
            $lastUsername = $this->authUtils->getLastUsername();

            return $this->twig->renderResponse("@App/Security/login.html.twig",
                array(
                    // last username entered by the user
                    'last_username' => $lastUsername,
                    'error' => $exception,
                ));
        }
    }

}