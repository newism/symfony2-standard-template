<?php

namespace Nsm\Bundle\ApiBundle\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Nsm\Bundle\ApiBundle\Entity\Project;
use Nsm\Bundle\ApiBundle\Entity\Task;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InvitationSubscriber implements EventSubscriber
{
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array();
    }

}
