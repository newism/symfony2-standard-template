<?php

namespace Nsm\Bundle\ContactCardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nsm\Bundle\CoreBundle\Entity\EntityInterface;

/**
 * Address
 */
class Address implements EntityInterface
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $typeCustom;

    /**
     * @var string
     */
    private $value;

    /**
     * @var boolean
     */
    private $preferred;

    /**
     * @var \Nsm\Bundle\ContactCardBundle\Entity\ContactCard
     */
    private $contactCard;


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
     * Set type
     *
     * @param string $type
     * @return Address
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set typeCustom
     *
     * @param string $typeCustom
     * @return Address
     */
    public function setTypeCustom($typeCustom)
    {
        $this->typeCustom = $typeCustom;

        return $this;
    }

    /**
     * Get typeCustom
     *
     * @return string 
     */
    public function getTypeCustom()
    {
        return $this->typeCustom;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Address
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set preferred
     *
     * @param boolean $preferred
     * @return Address
     */
    public function setPreferred($preferred)
    {
        $this->preferred = $preferred;

        return $this;
    }

    /**
     * Get preferred
     *
     * @return boolean 
     */
    public function getPreferred()
    {
        return $this->preferred;
    }

    /**
     * Set contactCard
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard
     * @return Address
     */
    public function setContactCard(\Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard = null)
    {
        $this->contactCard = $contactCard;

        return $this;
    }

    /**
     * Get contactCard
     *
     * @return \Nsm\Bundle\ContactCardBundle\Entity\ContactCard 
     */
    public function getContactCard()
    {
        return $this->contactCard;
    }
}
