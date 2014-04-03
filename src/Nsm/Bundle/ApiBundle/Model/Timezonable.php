<?php

namespace Nsm\Bundle\ApiBundle\Model;

/**
 * Timezonable trait.
 */
trait Timezonable
{
    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @param \DateTimeZone $timezone
     *
     * @return $this
     */
    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @return \DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}
