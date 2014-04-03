<?php

namespace Nsm\Bundle\ApiBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Nsm\Bundle\ApiBundle\Entity\Activity;
use Nsm\Bundle\ApiBundle\Entity\ActivityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ActivitySubscriber implements EventSubscriber
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
    public function getSubscribedEvents()
    {
        return array();
    }

}
