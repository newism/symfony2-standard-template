<?php

namespace Nsm\Bundle\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{
    /**
     * @param null $criteria
     * @param null $alias
     *
     * @return mixed
     */
    public function filter($criteria = null, $alias = null);

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
