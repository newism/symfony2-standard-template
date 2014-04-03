<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nsm\Bundle\ApiBundle\Entity\TaskRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 *
 * @Serializer\ExclusionPolicy("all")
 *
 * @Hateoas\Relation("self", href = @Hateoas\Route("tasks_read", parameters = { "id" = "expr(object.getId())" }))
 * @Hateoas\Relation("project", href = @Hateoas\Route("projects_read", parameters = { "id" = "expr(object.getProject().getId())" }))
 * @Hateoas\Relation("activities", href = @Hateoas\Route("activities_browse", parameters = { "task" = "expr(object.getId())" }))
 */
class Task extends AbstractEntity
{
    use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\SoftDeletable\SoftDeletable,
        ORMBehaviors\Blameable\Blameable,
        ORMBehaviors\Timezoneable\Timezoneable;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     * @Serializer\Groups({"task_list", "task_details"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"task_list", "task_details"})
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     */
    protected $project;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="task", cascade="remove");
     */
    protected $activities;

    /**
     * @var integer $activityDurationSum
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $activityDurationSum;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activities = new ArrayCollection();
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

    /**
     * Add activity
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Activity $activity
     *
     * @return Task
     */
    public function addActivity(\Nsm\Bundle\ApiBundle\Entity\Activity $activity)
    {
        $this->activities[] = $activity;
        $this->addActivityDuration($activity->getDuration());

        return $this;
    }

    /**
     * Remove activity
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Activity $activity
     */
    public function removeActivity(\Nsm\Bundle\ApiBundle\Entity\Activity $activity)
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
