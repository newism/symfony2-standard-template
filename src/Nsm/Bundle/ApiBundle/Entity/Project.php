<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FSC\HateoasBundle\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nsm\Bundle\ApiBundle\Entity\ProjectRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("custom", custom={"id"})
 *
 * @Hateoas\Relation("projects", href = @Hateoas\Route("projects_index"))
 * @Hateoas\Relation("self", href = @Hateoas\Route("projects_show", parameters = { "id" = ".id" }))
 * @Hateoas\Relation("tasks", href = @Hateoas\Route("tasks_index", parameters = { "project" = ".id" }))
 */
class Project extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"project_list", "project_details", "task_list", "task_details"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"project_list", "project_details", "task_list", "task_details"})
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"project_list", "project_details", "task_details"})
     */
    protected $description;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project");
     */
    protected $tasks;

    /**
     * @Serializer\VirtualProperty
     * @Serializer\Groups({"project_list"})
     * @return int
     */
    public function getTaskCount()
    {
        return count($this->getTasks());
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
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add tasks
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Task $tasks
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
     * Set description
     *
     * @param string $description
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
}
