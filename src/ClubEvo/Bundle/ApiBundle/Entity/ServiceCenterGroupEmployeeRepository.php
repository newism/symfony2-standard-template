<?php

namespace ClubEvo\Bundle\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

use Nsm\Bundle\CoreBundle\Entity\AbstractRepository;

/**
 * Service Center Group EmployeeRepository
 */
class ServiceCenterGroupEmployeeRepository extends AbstractRepository
{
    /**
     * @param array $criteria
     * @param bool  $removeEmpty
     *
     * @return $this
     */
    public function sanatiseCriteria(array $criteria, $removeEmpty = true)
    {
        return parent::sanatiseCriteria($criteria);
    }
}
