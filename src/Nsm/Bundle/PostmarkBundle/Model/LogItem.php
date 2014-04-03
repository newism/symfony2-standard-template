<?php

namespace Nsm\Bundle\PostmarkBundle\Model;

/**
 * Class LogItem
 * @package Nsm\Bundle\PostmarkBundle\Model
 */
class LogItem
{
    /**
     * @var integer
     */
    private $id;


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
     * @var string
     */
    private $name;

    /**
     * @var SpoolItem
     */
    private $spoolItem;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return LogItem
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set spoolItem
     *
     * @param SpoolItem $spoolItem
     *
     * @return LogItem
     */
    public function setSpoolItem(SpoolItem $spoolItem = null)
    {
        $this->spoolItem = $spoolItem;

        return $this;
    }

    /**
     * Get spoolItem
     *
     * @return SpoolItem
     */
    public function getSpoolItem()
    {
        return $this->spoolItem;
    }
}
