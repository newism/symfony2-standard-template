<?php

namespace Nsm\Bundle\ApiBundle\Tests\Entity;

use Liip\FunctionalTestBundle\Test\WebTestCase;

use Nsm\Bundle\ApiBundle\Entity\Project;
use Nsm\Bundle\ApiBundle\Entity\ProjectManager;

use Nsm\Bundle\ApiBundle\Entity\Activity;
use Nsm\Bundle\ApiBundle\Entity\ActivityManager;

use Nsm\Bundle\ApiBundle\Entity\Task;
use Nsm\Bundle\ApiBundle\Entity\TaskManager;

use Nsm\Bundle\ApiBundle\Form\Model\DateRange;

class ActivityFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var ProjectManager $projectManager
     */
    private $projectManager;

    /**
     * @var TaskManager $taskManager
     */
    private $taskManager;

    /**
     * @var ActivityManager $activityManager
     */
    private $activityManager;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $container             = static::$kernel->getContainer();
        
        $this->em              = $container->get('doctrine')->getManager();
        
        $this->projectManager = $container->get('project.manager');
        $this->taskManager = $container->get('task.manager');
        $this->activityManager = $container->get('activity.manager');
        
        parent::setUp();
    }

    /**
     * When an activity duration is updated the task duration should also be updated
     * When a task duration is updated the project duration should also be updated
     */
    public function testDurationUpdateBubblesToTaskAndProject()
    {
        $this->loadFixtures([]);

        /** @var Project $project */
        $project = $this->projectManager->create(
            [
                'title' => 'Project 1'
            ]
        );
        $this->projectManager->persist($project, true);

        /** @var Task $task */
        $task = $this->taskManager->create(
            [
                'title'    => 'Task 1',
                'project'  => $project
            ]
        );

        $this->taskManager->persist($task, true);

        /** @var Activity $activity */
        $activity = $this->activityManager->create(
            [
                'title'    => 'Activity 1',
                'task'  => $task,
                'duration' => 600
            ]
        );

        $this->activityManager->persist($activity, true);

        $this->assertEquals($task->getActivityDurationSum(), 600);
        $this->assertEquals($project->getTaskDurationSum(), 600);
    }

    /**
     * When an activity is stopped it's duration and endtime should be updated
     */
    public function testDurationUpdateOnStop()
    {
        $this->loadFixtures([]);

        $start = new \DateTime('-600 seconds');
        /** @var Activity $activity */
        $activity = $this->activityManager->create(
            [
                'title'     => 'Activity 1',
                'duration'  => 0,
                'dateRange' => new DateRange($start),
            ]
        );
        $activity->stop();
        $this->assertEquals(time(), $activity->getDateRange()->getEnd()->getTimestamp());
        $this->assertAttributeEquals(600, 'duration', $activity);
    }

    /**
     * When an activity has its duration updated
     * It's dateRange-end should be updated
     */
    public function testSetEndOnDurationChange(){
        $this->assertTrue(false);
    }


    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }
}
