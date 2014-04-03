<?php

namespace Nsm\Bundle\ApiBundle\Model;

/**
 * TimeZonable trait.
 */
trait TimeZonable
{
    /**
     * @var \DateTimeZone
     */
    protected $timeZone;

    /**
     * @param \DateTimeZone $timeZone
     *
     * @return $this
     */
    public function setTimeZone(\DateTimeZone $timeZone)
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * @return \DateTimeZone
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }
}
