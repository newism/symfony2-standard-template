<?php

namespace Nsm\Bundle\ApiBundle\EventListener;

use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use ReflectionObject;
use ReflectionProperty;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TimezoneSubscriber implements EventSubscriber
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
        $events = array(
//            Events::preUpdate,
//            Events::prePersist,
//            Events::preRemove,
        );

        return $events;
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $zone = new \DateTimeZone('UTC');
        $entityManager = $eventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $entity = $eventArgs->getEntity();

        $classMetadata = $entityManager->getClassMetadata(get_class($entity));

        $reflect = new ReflectionObject($entity);
        foreach ($reflect->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED) as $prop) {

            $oldValue = $prop->getValue($entity);
            if (!$oldValue instanceof DateTime || $oldValue->getTimezone()->getName() === 'UTC') {
                $prop->setAccessible(false);
                continue;
            }
            $oldValue->setTimezone($zone);
            $prop->setValue($entity, $oldValue);
            $prop->setAccessible(false);

            $unitOfWork->recomputeSingleEntityChangeSet(
                $entityManager->getClassMetadata(get_class($entity)),
                $entity
            );
        }
//
//        if ($this->isEntitySupported($classMetadata->reflClass, true)) {
//            if (!$entity->isBlameable()) {
//                return;
//            }
//            $user = $this->getUser();
//            if ($this->isValidUser($user)) {
//                $oldValue = $entity->getUpdatedBy();
//                $entity->setUpdatedBy($user);
//                $uow->propertyChanged($entity, 'updatedBy', $oldValue, $user);
//
//                $uow->scheduleExtraUpdate(
//                    $entity,
//                    [
//                        'updatedBy' => [$oldValue, $user],
//                    ]
//                );
//            }
//        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        //$this->updateTimezone($args);
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        // $this->updateTimezone($args);
    }

    public function updateTimezone(LifecycleEventArgs $args)
    {
        $zone = new \DateTimeZone('UTC');

        //Get hold of the unit of work.
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $entity = $args->getObject();

        $reflect = new ReflectionObject($entity);
        foreach ($reflect->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED) as $prop) {
            $prop->setAccessible(true);
            $value = $prop->getValue($entity);
            if (!$value instanceof DateTime || $value->getTimezone()->getName() === 'UTC') {
                $prop->setAccessible(false);
                continue;
            }
            $value->setTimezone($zone);
            $prop->setValue($entity, $value);
            $prop->setAccessible(false);
            $unitOfWork->recomputeSingleEntityChangeSet(
                $entityManager->getClassMetadata(get_class($entity)),
                $entity
            );
        }
    }

}
