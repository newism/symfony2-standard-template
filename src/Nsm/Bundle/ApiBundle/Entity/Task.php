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
     * @var string
     */
    protected $tags;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var Feature
     */
    protected $feature;

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
     * @param \Nsm\Bundle\ApiBundle\Entity\Activity $activities
     *
     * @return Task
     */
    public function addActivity(\Nsm\Bundle\ApiBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Activity $activities
     */
    public function removeActivity(\Nsm\Bundle\ApiBundle\Entity\Activity $activities)
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
     * Set feature
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Feature $feature
     *
     * @return Task
     */
    public function setFeature(\Nsm\Bundle\ApiBundle\Entity\Feature $feature = null)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * Get feature
     *
     * @return \Nsm\Bundle\ApiBundle\Entity\Feature 
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * Set project
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Project $project
     *
     * @return Task
     */
    public function setProject(\Nsm\Bundle\ApiBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Nsm\Bundle\ApiBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }
}
