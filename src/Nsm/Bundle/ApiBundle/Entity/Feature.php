<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\CoreBundle\Entity\AbstractEntity;

class Feature extends AbstractEntity
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
    protected $background;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var ArrayCollection
     */
    protected $tasks;

    /**
     * @var integer
     */
    protected $taskDurationSum;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taskDurationSum = 0;
        $this->tasks = new ArrayCollection();
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
     * @return Feature
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
     * @return Feature
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
     * Set taskDurationSum
     *
     * @param string $taskDurationSum
     *
     * @return Feature
     */
    public function setTaskDurationSum($taskDurationSum)
    {
        $this->taskDurationSum = $taskDurationSum;

        return $this;
    }

    /**
     * Get taskDurationSum
     *
     * @return string 
     */
    public function getTaskDurationSum()
    {
        return $this->taskDurationSum;
    }

    /**
     * Set project
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Project $project
     *
     * @return Feature
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

    /**
     * Add tasks
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Task $tasks
     *
     * @return Feature
     */
    public function addTask(\Nsm\Bundle\ApiBundle\Entity\Task $tasks)
    {
        $tasks->setFeature($this);
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Task $tasks
     */
    public function removeTask(\Nsm\Bundle\ApiBundle\Entity\Task $tasks)
    {
        $tasks->setFeature(null);
        $this->tasks->removeElement($tasks);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set background
     *
     * @param string $background
     *
     * @return Feature
     */
    public function setBackground($background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Get background
     *
     * @return string 
     */
    public function getBackground()
    {
        return $this->background;
    }
}
