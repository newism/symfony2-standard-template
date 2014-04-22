<?php

namespace Nsm\Bundle\ContactCardBundle\Entity;

/**
 * Telephone
 */
class Telephone
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
     *
     * @return Telephone
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
     * Set value
     *
     * @param string $value
     *
     * @return Telephone
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
     *
     * @return Telephone
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
     *
     * @return Telephone
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

