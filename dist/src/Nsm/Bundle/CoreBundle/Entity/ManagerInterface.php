<?php

namespace Nsm\Bundle\CoreBundle\Entity;

use Doctrine\ORM\EntityManager;

interface ManagerInterface
{
    public function __construct($class, EntityManager $entityManager);

    public function create(array $data = array());

    public function persist(EntityInterface $entity, $andFlush = false);

    public function remove(EntityInterface $entity, $andFlush = false);
}
