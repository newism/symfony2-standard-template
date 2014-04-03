<?php

namespace Nsm\Bundle\ApiBundle\Entity;

class AbstractEntity implements EntityInterface
{
    /**
     * @var int
     */
    protected $id;


    /**
     * @return string
     */
    public function __toString()
    {
        $title = method_exists($this, 'getTitle') ? $this->getTitle() : $this->getId();

        return (string)$title;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}
