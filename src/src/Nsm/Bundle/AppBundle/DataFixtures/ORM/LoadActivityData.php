<?php

namespace Nsm\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nsm\Bundle\AppBundle\Entity\ActivityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadActivityData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        /** @var ActivityManager $activityManager */
        $activityManager = $this->container->get('activity.manager');

        // Create 10 Activitys
        for ($i = 0; $i < 10; $i += 1) {
            
            $key      = sprintf('Activity %s', $i);
            $activity = $activityManager->create(
                [
                    'title' => $key,
                    'task'  => $this->getReference(sprintf('Task %s', $i))
                ]
            );

            $activityManager->persist($activity);
            $this->addReference($key, $activity);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
