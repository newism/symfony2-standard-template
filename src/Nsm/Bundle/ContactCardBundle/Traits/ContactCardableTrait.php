<?php

namespace Nsm\Bundle\ContactCardBundle\Traits;

use Nsm\Bundle\ContactCardBundle\Entity\ContactCard;

/**
 * ContactCardable trait.
 */
trait ContactCardableTrait
{
    /**
     * @var \Nsm\Bundle\ContactCardBundle\Entity\ContactCard
     */
    private $contactCard;

    /**
     * @param ContactCard $contactCard
     *
     * @return $this
     */
    public function setContactCard(ContactCard $contactCard)
    {
        $this->contactCard = $contactCard;

        return $this;
    }

    /**
     * @return ContactCard
     */
    public function getContactCard()
    {
        return $this->contactCard;
    }
}
