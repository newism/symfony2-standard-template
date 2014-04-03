<?php
namespace Nsm\Bundle\ApiBundle\EventListener;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\DBAL\Connection;

/**
 * TimeZoneListener
 */
class TimeZoneListener
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
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param \Doctrine\DBAL\Connection                                 $connection
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        Connection $connection
    )
    {
        $this->securityContext = $securityContext;
        $this->connection      = $connection;
    }

    /**
     * onKernelRequest
     */
    public function onKernelRequest()
    {
        if (!$this->securityContext->isGranted('ROLE_USER')) {
            return;
        }

        $user = $this->securityContext->getToken()->getUser();
        if (!$user->getTimeZone()) {
            return;
        }

        /** @var \DateTimeZone $dateTimeZone */
        $dateTimeZone = $user->getTimeZone();

        date_default_timezone_set($dateTimeZone->getName());
        $this->connection->query("SET time_zone='+00:00';");
    }
}
