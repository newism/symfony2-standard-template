<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Nsm\Bundle\UserBundle\Entity\User;


/**
 * Invitation
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Invitation extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $sent;

    /**
     * @var \DateTime $expiresAt
     *
     * @ORM\Column(name="expiresAt", type="datetime")
     */
    protected $expiresAt;

    /**
     * @var \DateTime $claimedAt
     *
     * @ORM\Column(name="claimedAt", type="datetime", nullable=true)
     */
    protected $claimedAt;

    /**
     * @var User $claimedBy
     *
     * @ORM\OneToOne(targetEntity="\Nsm\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="claimedBy", referencedColumnName="id", nullable=true)
     */
    protected $claimedBy;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->sent = false;
        $this->code = substr(md5(uniqid(rand(), true)), 0, 20);
        $this->setExpiresAt(new \DateTime('+7 days'));
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
     * Set code
     *
     * @param string $code
     *
     * @return Invitation
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Invitation
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     *
     * @return Invitation
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return Invitation
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set claimedAt
     *
     * @param \DateTime $claimedAt
     *
     * @return Invitation
     */
    public function setClaimedAt($claimedAt)
    {
        $this->claimedAt = $claimedAt;

        return $this;
    }

    /**
     * Get claimedAt
     *
     * @return \DateTime
     */
    public function getClaimedAt()
    {
        return $this->claimedAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Invitation
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Invitation
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set claimedBy
     *
     * @param \Nsm\Bundle\UserBundle\Entity\User $claimedBy
     *
     * @return Invitation
     */
    public function setClaimedBy(\Nsm\Bundle\UserBundle\Entity\User $claimedBy = null)
    {
        $this->claimedBy = $claimedBy;

        return $this;
    }

    /**
     * Get claimedBy
     *
     * @return \Nsm\Bundle\UserBundle\Entity\User
     */
    public function getClaimedBy()
    {
        return $this->claimedBy;
    }
}
