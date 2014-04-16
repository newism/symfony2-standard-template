<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\CoreBundle\Entity\AbstractEntity;

class Task extends AbstractEntity
{
    use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\SoftDeletable\SoftDeletable,
        ORMBehaviors\Blameable\Blameable;

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var ArrayCollection
     */
    protected $activities;

    /**
     * @var integer
     */
    protected $activityDurationSum;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activities = new ArrayCollection();
        $this->activityDurationSum = 0;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Task
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set project
     *
     * @param Project $project
     *
     * @return Task
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add activity
     *
     * @param Activity $activity
     *
     * @return Task
     */
    public function addActivity(Activity $activity)
    {
        $this->activities[] = $activity;
        $this->modifyActivityDurationSum($activity->getDuration());

        return $this;
    }

    /**
     * Remove activity
     *
     * @param Activity $activity
     */
    public function removeActivity(Activity $activity)
    {
        $this->activities->removeElement($activity);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Set activityDurationSum
     *
     * @param integer $activityDurationSum
     *
     * @return Task
     */
    public function setActivityDurationSum($activityDurationSum)
    {
        $this->activityDurationSum = $activityDurationSum;

        return $this;
    }

    /**
     * Get activityDurationSum
     *
     * @return integer
     */
    public function getActivityDurationSum()
    {
        return $this->activityDurationSum;
    }

    /**
     * @param $duration
     *
     * @return $this
     */
    public function modifyActivityDurationSum($duration)
    {
        $this->activityDurationSum += $duration;

        return $this;
    }
}
