<?php

namespace Nsm\Bundle\ContactCardBundle\Entity;

/**
 * ContactCard interface.
 */
interface ContactCardableInterface
{
    /**
     * @param ContactCard $contactCard
     *
     * @return $this
     */
    public function setContactCard(ContactCard $contactCard);

    /**
     * @return ContactCard
     */
    public function getContactCard();
}
