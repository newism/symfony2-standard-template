<?php

namespace Nsm\Bundle\PostmarkBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class SpoolItem
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $logItems;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logItems = new ArrayCollection();
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;

    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $logItems
     *
     * @return $this
     */
    public function setLogItems($logItems)
    {
        $this->logItems = $logItems;

        return $this;

    }

    /**
     * @return ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getLogItems()
    {
        return $this->logItems;
    }

    /**
     * @param LogItem $logItem
     *
     * @return SpoolItem
     */
    public function addLogItem(LogItem $logItem)
    {
        $logItem->setSpoolItem($this);
        $this->logItems[] = $logItem;

        return $this;
    }

    /**
     * @param LogItem $logItem
     *
     * @return $this
     */
    public function removeLogItem(LogItem $logItem)
    {
        $logItem->setSpoolItem(null);
        $this->logItems->removeElement($logItem);

        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;

    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}
