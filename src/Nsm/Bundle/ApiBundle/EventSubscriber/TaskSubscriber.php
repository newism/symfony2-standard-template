<?php

namespace Nsm\Bundle\ApiBundle\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Nsm\Bundle\ApiBundle\Entity\Task;
use Nsm\Bundle\ApiBundle\Entity\TaskManager;

/**
 * Task subscriber.
 *
 */
class TaskSubscriber implements EventSubscriber
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
        return array(
            Events::preFlush
        );
    }

    /**
     * @param PreFlushEventArgs $eventArgs
     */
    public function preFlush(PreFlushEventArgs $eventArgs)
    {
        $em  = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $updatedTasks = new ArrayCollection();

        $updatedEntities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($updatedEntities as $entity) {

            $changeSet = $uow->getEntityChangeSet($entity);

            if ($entity instanceof Activity) {
                /** @var Activity $entity */

                // Modify task duration
                if (true === array_key_exists('duration', $changeSet)) {
                    $durationDifference = (int)($changeSet['duration'][1] - $changeSet['duration'][0]);
                    if (0 !== $durationDifference) {
                        $task = $entity->getTask();
                        $task->modifyActivityDurationSum($durationDifference);
                        $em->persist($task);
                        $meta = $em->getClassMetadata(get_class($task));
                        $uow->recomputeSingleEntityChangeSet($meta, $task);
                    }
                }

            }
        }

        $deletedEntities = $uow->getScheduledEntityDeletions();

        foreach ($deletedEntities as $entity) {
            if (!$entity instanceof Task) {
                return;
            }

            $changeSet = $uow->getEntityChangeSet($entity);

        }

    }
}
