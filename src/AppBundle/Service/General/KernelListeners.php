<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 30/04/2016
 * Time: 08:26
 */

namespace AppBundle\Service\General;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class KernelListeners
{

    /**
     * @var Session
     */
    private $session;

    /**
     * @var SourceService
     */
    private $sourceService;

    public function __construct(Session $session, SourceService $sourceService)
    {
        $this->session = $session;
        $this->sourceService = $sourceService;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {

        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        if ($request->getMethod() == 'GET') {
            if ($request->query->has('__st')) {
                //Request is coming from other place
                $sourceToken = $request->query->get('__st');
                //pass the token to the source service to check it and save it
                $this->sourceService->setSource($sourceToken);
                //Creating the redirect response
                $params = $request->query->getIterator();
                unset($params['__st']);
                if (count($params)>0){
                    $redirectUrl = $request->getSchemeAndHttpHost() . $request->getPathInfo() . "?" . http_build_query($params);
                }else{
                    $redirectUrl = $request->getSchemeAndHttpHost() . $request->getPathInfo();
                }
                $event->setResponse(new RedirectResponse($redirectUrl));
            }else{
                $this->sourceService->verifySource();
            }
        }
    }
}