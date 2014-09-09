<?php

namespace Nsm\Bundle\ContactCardBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Nsm\Bundle\CoreBundle\Entity\AbstractManager;

/**
 * ContactCard manager
 */
class ContactCardManager extends AbstractManager
{
    public function getPreferredElementFromCollection(ArrayCollection $collection)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('preferred', true));

        $collection->matching($criteria);

        return $collection->first() ?: null;
    }
}

