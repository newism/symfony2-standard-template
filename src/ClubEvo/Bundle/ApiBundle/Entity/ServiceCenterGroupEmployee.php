<?php

namespace ClubEvo\Bundle\ApiBundle\Entity;

/**
 * ServiceCenterGroupEmployee
 */
class ServiceCenterGroupEmployee extends Person
{
    /**
     * @var integer
     */
    private $id;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return ServiceCenterGroupEmployee
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
}

