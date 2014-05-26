<?php

namespace Nsm\Bundle\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nsm\Bundle\ApiBundle\Entity\FeatureManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFeatureData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        /** @var TaskManager $featureManager */
        $featureManager = $this->container->get('feature.manager');

        // Create 10 Tasks
        for ($i = 0; $i < 10; $i += 1) {
            $key  = sprintf('Task List %s', $i);
            $feature = $featureManager->create(
                [
                    'title' => $key,
                    'project' => $this->getReference(sprintf('Project %s', $i))
                ]
            );
            $featureManager->persist($feature);
            $this->addReference($key, $feature);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }

}
