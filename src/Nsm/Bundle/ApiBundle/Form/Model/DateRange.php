<?php

namespace Nsm\Bundle\ApiBundle\Form\Model;

use \DateTime;

class DateRange
{
    /**
     * @var \DateTime|DateTime
     */
    public $start;

    /**
     * @var \DateTime|DateTime
     */
    public $end;

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct(\DateTime $start = null, \DateTime $end = null)
    {
//        if (null === $start) {
//            $start = new DateTime();
//            $start->setTime(0, 0, 0);
//        }
//
//        if (null === $end) {
//            $end = new DateTime();
//            $end->setTime(23, 59, 59);
//        }

        $this->start = $start;
        $this->end   = $end;
    }
}
