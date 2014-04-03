<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nsm\Bundle\ApiBundle\Entity\ProjectRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("custom", custom={"id"})
 * @Serializer\XmlRoot("project")
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route("projects_read", parameters = { "id" = "expr(object.getId())" }),
 *      exclusion = @Hateoas\Exclusion(groups = {"project_index", "project_details"})
 * )
 * @Hateoas\Relation(
 *      "tasks",
 *      embedded = @Hateoas\Embedded("expr(object.getTasks())"),
 *      href = @Hateoas\Route("tasks_browse", parameters = { "project" = "expr(object.getId())" }),
 *      exclusion = @Hateoas\Exclusion(groups = {"project_details"})
 * )
 */
class Project extends AbstractEntity
{
    use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\SoftDeletable\SoftDeletable,
        ORMBehaviors\Blameable\Blameable;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"project_list", "project_details", "task_list", "task_details"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"project_list", "project_details", "task_list", "task_details"})
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=255, nullable=true)
     * @Serializer\Expose()
     * @Serializer\Groups({"project_list", "project_details", "task_details"})
     */
    protected $description;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project", cascade="remove");
     */
    protected $tasks;

    /**
     * @var integer $taskDurationSum
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $taskDurationSum;

    /**
     * @ORM\OneToOne(targetEntity="File", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $avatar;
    
    /**
     * @Serializer\VirtualProperty
     * @Serializer\Groups({"project_list"})
     * @Serializer\Groups({"project_details"})
     * @return int
     */
    public function getTaskCount()
    {
        return $this->getTasks()->count();
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\Groups({"project_details"})
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
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param \Nsm\Bundle\ApiBundle\Entity\Task $tasks
     *
     * @return Project
     */
    public function addTask(\Nsm\Bundle\ApiBundle\Entity\Task $tasks)
    {
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
     * Set taskDurationSum
     *
     * @param integer $taskDurationSum
     *
     * @return Project
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
     * Set avatar
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\File $avatar
     *
     * @return Task
     */
    public function setAvatar(\Nsm\Bundle\ApiBundle\Entity\File $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \Nsm\Bundle\ApiBundle\Entity\File
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
}
