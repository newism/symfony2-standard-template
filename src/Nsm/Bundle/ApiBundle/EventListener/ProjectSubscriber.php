<?php

namespace Nsm\Bundle\ApiBundle\EventListener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Nsm\Bundle\ApiBundle\Entity\Project;
use Nsm\Bundle\ApiBundle\Entity\Task;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProjectSubscriber implements EventSubscriber
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
     * @param OnFlushEventArgs $eventArgs
     */
    public function preFlush(PreFlushEventArgs $eventArgs)
    {
        $em  = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $updatedProjects = new ArrayCollection();

        $updatedEntities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($updatedEntities as $entity) {

            $changeSet = $uow->getEntityChangeSet($entity);

            if ($entity instanceof Task) {
                /** @var Task $entity */

                // Modify project duration
                if (true === array_key_exists('activityDurationSum', $changeSet)) {
                    $durationDifference = (int)($changeSet['activityDurationSum'][1] - $changeSet['activityDurationSum'][0]);
                    if(0 !== $durationDifference) {
                        $project            = $entity->getProject();
                        $project->modifyTaskDurationSum($durationDifference);
                        $em->persist($project);
                        $meta = $em->getClassMetadata(get_class($project));
                        $uow->recomputeSingleEntityChangeSet($meta, $project);
                    }
                }

            }
        }

        $deletedEntities = $uow->getScheduledEntityDeletions();

        foreach ($deletedEntities as $entity) {
            if (!$entity instanceof Project) {
                return;
            }

            $changeSet = $uow->getEntityChangeSet($entity);

        }

    }

}
