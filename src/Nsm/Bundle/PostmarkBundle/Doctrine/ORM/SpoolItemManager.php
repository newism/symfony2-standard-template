<?php

namespace Nsm\Bundle\PostmarkBundle\Doctrine\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Nsm\Bundle\PostmarkBundle\Model\SpoolItemManager as BaseSpoolItemManager;

/**
 * Class SpoolItemManager
 * @package Nsm\Bundle\PostmarkBundle\Doctrine\ORM
 */
class SpoolItemManager extends BaseSpoolItemManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * @param ObjectManager $om
     * @param               $class
     */
    public function __construct(ObjectManager $om, $class)
    {
        $this->objectManager = $om;
        $this->repository    = $om->getRepository($class);

        $metadata    = $om->getClassMetadata($class);
        $this->class = $metadata->getName();

    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
