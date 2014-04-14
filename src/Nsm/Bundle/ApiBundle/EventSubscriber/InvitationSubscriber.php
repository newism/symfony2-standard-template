<?php

namespace Nsm\Bundle\ApiBundle\EventSubscriber;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Nsm\Bundle\ApiBundle\Entity\Invitation;
use Nsm\Bundle\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvitationSubscriber implements EventSubscriberInterface
{

    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onRegistrationInitialize(GetResponseUserEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        /** @var Request $request */
        $request = $event->getRequest();

        if ($request->isMethod('GET')) {

            $invitationCode = $request->query->get('invitationCode');

            // Find the invitation
            $invitation = new Invitation();
            $invitation->setCode($invitationCode);
            $user->setInvitation($invitation);

//            // No Invitation?
//            $response = new RedirectResponse($this->router->generate('dashboard_browse'));
//            $event->setResponse($response);

        }
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        // On success we claim the invitation
    }

}
