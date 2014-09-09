<?php

namespace Nsm\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nsm\Bundle\AppBundle\Entity\TaskManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTaskData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var TaskManager $taskManager */
        $taskManager = $this->container->get('task.manager');

        // Create 10 Tasks
        for ($i = 0; $i < 10; $i += 1) {
            $key  = sprintf('Task %s', $i);
            $task = $taskManager->create(
                [
                    'title' => $key,
                    'project' => $this->getReference(sprintf('Project %s', $i))
                ]
            );
            $taskManager->persist($task);
            $this->addReference($key, $task);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

}
