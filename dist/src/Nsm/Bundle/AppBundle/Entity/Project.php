<?php

namespace Nsm\Bundle\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\CoreBundle\Entity\AbstractEntity;

class Project extends AbstractEntity
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
     * @var ArrayCollection
     */
    protected $tasks;

    /**
     * @var integer $taskDurationSum
     */
    protected $taskDurationSum;

    /**
     * @return int
     */
    public function getTaskCount()
    {
        return $this->getTasks()->count();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaskIds()
    {
        return $this->getTasks()->map(
            function ($task) {
                return $task->getId();
            }
        );
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->features = new ArrayCollection();

        $this->taskDurationSum = 0;
    }

    /**
     * Set taskDurationSum
     *
     * @param integer $taskDurationSum
     *
     * @return $this
     */
    public function setTaskDurationSum($taskDurationSum)
    {
        $this->taskDurationSum = $taskDurationSum;

        return $this;
    }

    /**
     * Get taskDurationSum
     *
     * @return integer
     */
    public function getTaskDurationSum()
    {
        return $this->taskDurationSum;
    }

    /**
     * @param $duration
     *
     * @return $this
     */
    public function modifyTaskDurationSum($duration)
    {
        $this->taskDurationSum += $duration;

        return $this;
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
     * @return Project
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
     * Add tasks
     *
     * @param \Nsm\Bundle\AppBundle\Entity\Task $tasks
     *
     * @return Project
     */
    public function addTask(\Nsm\Bundle\AppBundle\Entity\Task $tasks)
    {
        $tasks->setProject($this);
        $this->tasks[] = $tasks;

        return $this;
    }

    /**
     * Remove tasks
     *
     * @param \Nsm\Bundle\AppBundle\Entity\Task $tasks
     */
    public function removeTask(\Nsm\Bundle\AppBundle\Entity\Task $tasks)
    {
        $tasks->setProject(null);
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
     * @var \Nsm\Bundle\ContactCardBundle\Entity\ContactCard
     */
    private $contactCard;


    /**
     * Set contactCard
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard
     *
     * @return Project
     */
    public function setContactCard(\Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard = null)
    {
        $this->contactCard = $contactCard;

        return $this;
    }

    /**
     * Get contactCard
     *
     * @return \Nsm\Bundle\ContactCardBundle\Entity\ContactCard 
     */
    public function getContactCard()
    {
        return $this->contactCard;
    }
}
