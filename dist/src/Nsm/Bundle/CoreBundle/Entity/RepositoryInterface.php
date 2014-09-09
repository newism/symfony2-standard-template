<?php

namespace Nsm\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{
    /**
     * @param array $criteria
     * @param bool  $removeEmpty
     *
     * @return mixed
     */
    public function sanatiseCriteria(array $criteria, $removeEmpty = true);

    /**
     * @param $collection
     *
     * @return mixed
     */
    public function transformCollectionToIdArray($collection);
    
}
