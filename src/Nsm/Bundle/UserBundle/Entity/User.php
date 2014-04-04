<?php

namespace Nsm\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Entity
 * @ORM\Table(name="NsmUser")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
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
     * @Serializer\Groups({"user_list", "user_details"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $mailboxHash;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Please enter your first name.", groups={"Registration", "Profile"})
     */
    protected $firstName;

    /**
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @Assert\NotBlank(message="Please enter your time zone.", groups={"Registration", "Profile"})
     * @ORM\Column(type="timezone", nullable=true)
     */
    protected $timeZone;

    /**
     * @Assert\NotBlank(message="Please enter your locale.", groups={"Registration", "Profile"})
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    protected $locale;

    protected $invitation;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Reset the mailbox hash
     */
    public function resetMailboxHash()
    {
        $mailboxHash = md5(uniqid(rand(), true));
        $this->setMailboxHash($mailboxHash);
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersistMailboxHash()
    {
        $this->resetMailboxHash();
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
     * Set mailboxHash
     *
     * @param string $mailboxHash
     *
     * @return User
     */
    public function setMailboxHash($mailboxHash)
    {
        $this->mailboxHash = $mailboxHash;

        return $this;
    }

    /**
     * Get mailboxHash
     *
     * @return string 
     */
    public function getMailboxHash()
    {
        return $this->mailboxHash;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set time zone
     *
     * @param string $timeZone
     *
     * @return User
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Get time zone
     *
     * @return string 
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * Set invitation
     *
     * @param \Nsm\Bundle\UserBundle\Entity\Invitation $invitation
     *
     * @return User
     */
    public function setInvitation(\Nsm\Bundle\UserBundle\Entity\Invitation $invitation = null)
    {
        $this->invitation = $invitation;

        return $this;
    }

    /**
     * Get invitation
     *
     * @return \Nsm\Bundle\UserBundle\Entity\Invitation 
     */
    public function getInvitation()
    {
        return $this->invitation;
    }

    /**
     * @param mixed $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

}
