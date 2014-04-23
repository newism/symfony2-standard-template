<?php

namespace ClubEvo\Bundle\ApiBundle\Entity;

/**
 * Organisation
 */
class Organisation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $acn;

    /**
     * @var string
     */
    private $abn;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $subclass;

    /**
     * @var \Nsm\Bundle\ContactCardBundle\Entity\ContactCard
     */
    private $contactCard;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return Organisation
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @return Organisation
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
     * Set acn
     *
     * @param string $acn
     *
     * @return Organisation
     */
    public function setAcn($acn)
    {
        $this->acn = $acn;

        return $this;
    }

    /**
     * Get acn
     *
     * @return string 
     */
    public function getAcn()
    {
        return $this->acn;
    }

    /**
     * Set abn
     *
     * @param string $abn
     *
     * @return Organisation
     */
    public function setAbn($abn)
    {
        $this->abn = $abn;

        return $this;
    }

    /**
     * Get abn
     *
     * @return string 
     */
    public function getAbn()
    {
        return $this->abn;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Organisation
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
     * Set class
     *
     * @param string $class
     *
     * @return Organisation
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string 
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set subclass
     *
     * @param string $subclass
     *
     * @return Organisation
     */
    public function setSubclass($subclass)
    {
        $this->subclass = $subclass;

        return $this;
    }

    /**
     * Get subclass
     *
     * @return string 
     */
    public function getSubclass()
    {
        return $this->subclass;
    }

    /**
     * Set contactCard
     *
     * @param \Nsm\Bundle\ContactCardBundle\Entity\ContactCard $contactCard
     *
     * @return Organisation
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

