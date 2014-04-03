<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\ApiBundle\Form\Model\DateRange;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Activity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Nsm\Bundle\ApiBundle\Entity\FileRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("custom", custom={"id"})
 */
class File extends AbstractEntity
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
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    protected $directory;
    
    /**
     * @var UploadedFile
     */
    protected $uploadedFile;

    function __construct($directory)
    {
        $this->directory = $directory;
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
     * @return File
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
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadedFile
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * Set directory
     *
     * @param string $directory
     *
     * @return File
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return string 
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
