<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\FormBundle\Form\Model\DateRange;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Activity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nsm\Bundle\ApiBundle\Entity\ActivityRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true)
 *
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("custom", custom={"id"})
 *
 * @Hateoas\Relation("self", href = @Hateoas\Route("activities_read", parameters = { "id" = "expr(object.getId())" }))
 * @Hateoas\Relation("task", href = @Hateoas\Route("tasks_read", parameters = { "id" = "expr(object.getTask().getId())" }))
 * @Hateoas\Relation("activities", href = @Hateoas\Route("activities_browse"))
 */
class Activity extends AbstractEntity
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
     * @Serializer\Groups({"activity_list", "activity_details", "task_list", "task_details"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     * @Serializer\Groups({"activity_list", "activity_details", "task_details"})
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="activities")
     * @Assert\NotNull()
     * @Serializer\Expose()
     * @Serializer\Groups({"activity_details", "activity_list"})
     */
    protected $task;

    /**
     * @var DateRange $dateRange
     */
    protected $dateRange;

    /**
     * @var \Datetime $startedAt
     *
     * The date time the timer was started. When a timer is stopped this time is used to calculate the duration.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $startedAt;

    /**
     * @var \Datetime $endedAt
     *
     * The date time the timer was ended. When a timer is stopped this time is used to calculate the duration.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $endedAt;

    /**
     * @var integer $duration
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $duration;

    /**
     *
     */
    public function __construct($startTimer = false)
    {
        $this->duration = 0;
        $this->setDateRange(new DateRange());

        if (true === $startTimer) {
            $this->start();
        }

        return $this;
    }

    /**
     * Start the activity
     *
     * @return $this
     */
    public function start()
    {
        if (null === $this->dateRange->getStart()) {
            $this->dateRange->setStart(new \DateTime());
        }

        return $this;
    }

    /**
     * Stop the timer
     *
     * @return $this
     */
    public function stop()
    {
        if (null === $this->dateRange->getEnd()) {
            $this->dateRange->setEnd(new \DateTime());
            $this->updateDuration();
        }

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
     * @return Activity
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
     * Construct the dateRange object from DB Data
     *w
     * @ORM\PostLoad
     */
    public function constructDateRange()
    {
        $this->dateRange = new DateRange($this->startedAt, $this->endedAt);
    }

    /**
     * Set dateRange
     *
     * @param DateRange $dateRange
     *
     * @return $this
     */
    public function setDateRange(DateRange $dateRange)
    {
        $this->dateRange = $dateRange;
        $this->startedAt = $dateRange->getStart();
        $this->endedAt   = $dateRange->getEnd();

        return $this;
    }

    /**
     * Get dateRange
     *
     * @return DateRange
     */
    public function getDateRange()
    {
        return $this->dateRange;
    }


    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Activity
     */
    public function setDuration($duration)
    {
        // Add the difference to the task
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Calculate the activity duration
     *
     * @return int
     */
    public function calculateDuration()
    {
        if (null === $this->dateRange->getEnd()) {
            return 0;
        }

        $duration = $this->dateRange->getEnd()->getTimestamp() - $this->dateRange->getStart()->getTimestamp();

        return $duration;
    }

    /**
     * Update the activity duration
     *
     * @return $this
     */
    public function updateDuration()
    {
        $this->setDuration($this->calculateDuration());

        return $this;
    }


    /**
     * Set task
     *
     * @param \Nsm\Bundle\ApiBundle\Entity\Task $task
     *
     * @return Activity
     */
    public function setTask(\Nsm\Bundle\ApiBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Nsm\Bundle\ApiBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

}
