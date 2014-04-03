<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\ORM\EntityManager;

abstract class AbstractManager implements ManagerInterface
{
    /**
     * @var string $class
     */
    protected $class;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @param               $class
     * @param EntityManager $entityManager
     */
    public function __construct($class, EntityManager $entityManager)
    {
        $this->class         = $class;
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository($this->class);
    }

    /**
     * @param array $data
     *
     * @return EntityInterface
     */
    public function create(array $data = null)
    {
        $entity = new $this->class();
        $entity = $this->setData($entity, $data);

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param array           $data
     *
     * @return EntityInterface
     */
    public function setData(EntityInterface $entity, array $data = null)
    {
        if (null === $data) {
            return $entity;
        }

        foreach ($data as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            $entity->{$methodName}($value);
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param bool            $andFlush
     *
     * @return EntityInterface
     */
    public function persist(EntityInterface $entity, $andFlush = false)
    {
        $this->entityManager->persist($entity);
        
        if (true === $andFlush) {
            $this->flush();
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param bool            $andFlush
     *
     * @return EntityInterface
     */
    public function remove(EntityInterface $entity, $andFlush = false)
    {
        $this->entityManager->remove($entity);
        
        if (true === $andFlush) {
            $this->flush();
        }
    }

    /**
     * Flush the entity manager
     *
     * @return $this
     */
    public function flush()
    {
        $this->entityManager->flush();

        return $this;
    }
}
