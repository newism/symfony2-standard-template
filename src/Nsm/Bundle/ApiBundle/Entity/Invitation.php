<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Nsm\Bundle\UserBundle\Entity\User;
use Nsm\Bundle\CoreBundle\Entity\AbstractEntity;

class Invitation extends AbstractEntity
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
    protected $code;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var boolean
     */
    protected $sent;

    /**
     * @var \DateTime $expiresAt
     */
    protected $expiresAt;

    /**
     * @var \DateTime $claimedAt
     */
    protected $claimedAt;

    /**
     * @var User $claimedBy
     */
    protected $claimedBy;

    public function __construct()
    {
        $this->sent = false;
        $this->code = substr(md5(uniqid(rand(), true)), 0, 20);
        $this->setExpiresAt(new \DateTime('+7 days'));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $sent
     *
     * @return $this
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param $expiresAt
     *
     * @return $this
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param $claimedAt
     *
     * @return $this
     */
    public function setClaimedAt($claimedAt)
    {
        $this->claimedAt = $claimedAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClaimedAt()
    {
        return $this->claimedAt;
    }

    /**
     * @param User $claimedBy
     *
     * @return $this
     */
    public function setClaimedBy(User $claimedBy = null)
    {
        $this->claimedBy = $claimedBy;

        return $this;
    }

    /**
     * @return User
     */
    public function getClaimedBy()
    {
        return $this->claimedBy;
    }
}
