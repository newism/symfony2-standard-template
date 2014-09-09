<?php

namespace Nsm\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as Serializer;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\AppBundle\Entity\Invitation;
use Symfony\Component\Validator\Constraints as Assert;

class User extends BaseUser
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
    protected $mailboxHash;

    /**
     * @Assert\NotBlank(message="Please enter your first name.", groups={"Registration", "Profile"})
     */
    protected $firstName;

    /**
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     */
    protected $lastName;

    /**
     * @Assert\NotBlank(message="Please enter your time zone.", groups={"Registration", "Profile"})
     */
    protected $timeZone;

    /**
     * @Assert\NotBlank(message="Please enter your locale.", groups={"Registration", "Profile"})
     */
    protected $locale;

    /**
     * @var Invitation
     */
    protected $invitations;

    /**
     * @var string
     */
    private $githubId;

    /**
     * @var string
     */
    private $instagramId;

    protected $userEntityEvents;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->invitations = new ArrayCollection();
    }

    /**
     * @return $this
     */
    public function resetMailboxHash()
    {
        $mailboxHash = md5(uniqid(rand(), true));
        $this->setMailboxHash($mailboxHash);

        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $mailboxHash
     *
     * @return $this
     */
    public function setMailboxHash($mailboxHash)
    {
        $this->mailboxHash = $mailboxHash;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMailboxHash()
    {
        return $this->mailboxHash;
    }

    /**
     * @param $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param $timeZone
     *
     * @return $this
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @param $locale
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

    /**
     * @param $githubId
     */
    public function setGithubId($githubId)
    {
        $this->githubId = $githubId;
    }

    /**
     * @return string
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * @param $instagramId
     */
    public function setInstagramId($instagramId)
    {
        $this->instagramId = $instagramId;
    }

    /**
     * @return string
     */
    public function getInstagramId()
    {
        return $this->instagramId;
    }

    /**
     * Add invitations
     *
     * @param Invitation $invitations
     *
     * @return User
     */
    public function addInvitation(Invitation $invitations)
    {
        $this->invitations[] = $invitations;

        return $this;
    }

    /**
     * Remove invitations
     *
     * @param Invitation $invitations
     */
    public function removeInvitation(Invitation $invitations)
    {
        $this->invitations->removeElement($invitations);
    }

    /**
     * Get invitations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvitations()
    {
        return $this->invitations;
    }
}
