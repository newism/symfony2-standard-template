<?php

namespace Nsm\Bundle\AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nsm\Bundle\AppBundle\Entity\ProjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProjectData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        /** @var ProjectManager $projectManager */
        $projectManager = $this->container->get('project.manager');

        // Create 10 Projects
        for ($i = 0; $i < 10; $i += 1) {
            $key     = sprintf('Project %s', $i);
            $project = $projectManager->create(
                [
                    'title' => $key
                ]
            );
            $projectManager->persist($project);
            $this->addReference($key, $project);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}
