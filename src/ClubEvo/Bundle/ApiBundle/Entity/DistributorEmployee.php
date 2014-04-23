<?php

namespace ClubEvo\Bundle\ApiBundle\Entity;

/**
 * DistributorEmployee
 */
class DistributorEmployee extends Person
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $role;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return DistributorEmployee
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
     * Set role
     *
     * @param string $role
     *
     * @return DistributorEmployee
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string 
     */
    public function getRole()
    {
        return $this->role;
    }
}

