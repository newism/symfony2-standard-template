<?php

namespace ClubEvo\Bundle\ApiBundle\Entity;

/**
 * RoadsideAssistEmployee
 */
class RoadsideAssistEmployee extends Person
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $role;


    /**
     * Set id
     *
     * @param integer $id
     *
     * @return RoadsideAssistEmployee
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
     * @param integer $role
     *
     * @return RoadsideAssistEmployee
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return integer 
     */
    public function getRole()
    {
        return $this->role;
    }
}

