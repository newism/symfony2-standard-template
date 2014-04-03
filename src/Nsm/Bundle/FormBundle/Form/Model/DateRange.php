<?php

namespace Nsm\Bundle\FormBundle\Form\Model;

use DateTime;

class DateRange
{
    /**
     * @var DateTime
     */
    protected $start;

    /**
     * @var DateTime
     */
    protected $end;

    /**
     * @param DateTime $start
     * @param DateTime $end
     */
    public function __construct(DateTime $start = null, DateTime $end = null)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    /**
     * @param DateTime $start
     *
     * @return $this
     */
    public function setStart(DateTime $start = null)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param DateTime $end
     *
     * @return $this
     */
    public function setEnd(DateTime $end = null)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }
}
