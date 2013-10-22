<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FSC\HateoasBundle\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nsm\Bundle\ApiBundle\Entity\TaskRepository")
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation("tasks", href = @Hateoas\Route("tasks_index"))
 * @Hateoas\Relation("self", href = @Hateoas\Route("tasks_show", parameters = { "id" = ".id" }))
 * @Hateoas\Relation("project", href = @Hateoas\Route("projects_show", parameters = { "id" = ".project.id" }))
 */
class Task extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"task_list", "task_details"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"task_list", "task_details"})
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     * @Serializer\Expose()
     * @Serializer\Groups({"task_details", "task_list"})
     */
    protected $project;

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->getProject()->getId();
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
     * Set project
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Project $project
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
