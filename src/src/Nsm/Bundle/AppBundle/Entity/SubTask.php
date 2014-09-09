<?php

namespace Nsm\Bundle\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\CoreBundle\Entity\AbstractEntity;

class SubTask extends AbstractEntity
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
     * @var string
     */
    protected $tags;

    /**
     * @var Task
     */
    protected $task;

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
        $this->activityDurationSum = 0;
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Task
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
     * Set tags
     *
     * @param string $tags
     *
     * @return Task
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set activityDurationSum
     *
     * @param string $activityDurationSum
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
     * @return string 
     */
    public function getActivityDurationSum()
    {
        return $this->activityDurationSum;
    }

    /**
     * Add activities
     *
     * @param \Nsm\Bundle\AppBundle\Entity\Activity $activities
     *
     * @return Task
     */
    public function addActivity(\Nsm\Bundle\AppBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \Nsm\Bundle\AppBundle\Entity\Activity $activities
     */
    public function removeActivity(\Nsm\Bundle\AppBundle\Entity\Activity $activities)
    {
        $this->activities->removeElement($activities);
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
     * Set task
     *
     * @param \Nsm\Bundle\AppBundle\Entity\Task $task
     *
     * @return Task
     */
    public function setTask(\Nsm\Bundle\AppBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Nsm\Bundle\AppBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }
}
