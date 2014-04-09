<?php

namespace Nsm\Bundle\UserBundle\EventSubscriber;

use Doctrine\DBAL\Connection;
use Nsm\Bundle\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param SecurityContextInterface $securityContext
     * @param Connection               $connection
     * @param string                   $defaultLocale
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        Connection $connection,
        $defaultLocale = "en"
    ) {
        $this->securityContext = $securityContext;
        $this->connection = $connection;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        $events = array(
            'kernel.request' => array('setUserPreferences')
        );

        return $events;
    }

    /**
     * onKernelRequest
     */
    public function setUserPreferences(KernelEvent $event)
    {
        // Set the connect timezone to UTC
        $this->connection->query("SET time_zone='+00:00';");

        /** @var TokenInterface $token */
        $token = $this->securityContext->getToken();

        if (null === $token || !$this->securityContext->isGranted('ROLE_USER')) {
            return;
        }

        /** @var User $user */
        $user = $token->getUser();

        /** @var \DateTimeZone $dateTimeZone */
        $dateTimeZone = $user->getTimeZone();
        if ($dateTimeZone instanceof \DateTimeZone) {
            date_default_timezone_set($dateTimeZone->getName());
        }

        $locale = $user->getLocale();
        if (null === $locale) {
            $locale = $this->defaultLocale;
        }
        $request = $event->getRequest();
        $request->setLocale($locale);
    }
}
