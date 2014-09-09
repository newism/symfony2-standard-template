<?php

namespace Nsm\Bundle\HelpBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TipSubscriber implements EventSubscriber
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
