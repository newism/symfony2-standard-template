<?php

namespace Nsm\Bundle\ApiBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;

class RequestScopeSubscriber implements EventSubscriberInterface
{
    private $router;
    private $requestStack;

    /**
     * @param Router       $router
     * @param RequestStack $requestStack
     */
    public function __construct(Router $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered after the Router to have access to the _locale
            KernelEvents::REQUEST => array(array('onKernelRequest', 16)),
        );
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $request = $event->getRequest();
            $requestScope = $request->query->get('scope');
            // Unfortunately this doesn't work :(
            // Only parameters with valid token placeholders get generated in the router
            $this->router->getContext()->setParameter('scope', $requestScope);

        }
    }
}
