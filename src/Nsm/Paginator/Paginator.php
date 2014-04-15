<?php

namespace Nsm\Paginator;

use Doctrine\ORM\Tools\Pagination\Paginator as BasePaginator;

/**
 * Simple Doctrine Query Paginator
 *
 * $paginator = new Paginator($qb, false);
 *
 * var_dump(
 *  array(
 *      'perPage' => $paginator->getPerPage(),
 *      'currentPage' => $paginator->getCurrentPage(),
 *      'currentPageResultCount' => $paginator->getCurrentPageResultCount(),
 *      'currentPageFirstResultOffset' => $paginator->getCurrentPageFirstResultOffset(),
 *      'currentPageLastResultOffset' => $paginator->getCurrentPageLastResultOffset(),
 *      'hasPreviousPage' => $paginator->hasPreviousPage(),
 *      'previousPage' => $paginator->hasPreviousPage() ? $paginator->getPreviousPage() : false,
 *      'hasNextPage' => $paginator->hasNextPage(),
 *      'nextPage' => $paginator->hasNextPage() ? $paginator->getNextPage() : false,
 *      'pageCount' => $paginator->getPageCount(),
 *      'totalResultCount' => $paginator->count(),
 *      'canPaginate' => $paginator->canPaginate(),
 *      'currentPageOffsetRange' => $paginator->getCurrentPageOffsetRange(3),
 *      'currentPageResults' => $paginator->getCurrentPageResults(),
 *  )
 * );
 */
class Paginator extends BasePaginator
{
    /**
     * @var int
     */
    private $currentPage = 1;

    /**
     * @var int
     */
    private $perPage = 10;

    /**
     * @param \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder $query
     * @param bool                                           $fetchJoinCollection
     */
    public function __construct($query, $fetchJoinCollection = true)
    {
        parent::__construct($query, $fetchJoinCollection);

        $this->setCurrentPage($this->currentPage);
        $this->setPerPage($this->perPage);
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = intval($page);

        // Zero based index
        $this->getQuery()->setFirstResult($this->getCurrentPageFirstResultOffset() - 1);

        return $this;
    }


    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = intval($perPage);
        $this->getQuery()->setMaxResults($this->perPage);
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function getPageCount()
    {
        // No results must have one page
        return (0 === $this->count()) ? 1 : intval(ceil($this->count() / $this->getQuery()->getMaxResults()));
    }

    /**
     * @param $pageNumber
     *
     * @return int
     */
    public function calculateFirstResultOffset($pageNumber)
    {
        return ($pageNumber - 1) * $this->getQuery()->getMaxResults() + 1;
    }

    /**
     * @return int
     */
    public function getCurrentPageFirstResultOffset()
    {
        return $this->calculateFirstResultOffset($this->currentPage);
    }

    /**
     * @param $pageNumber
     *
     * @return int
     */
    public function calculateLastResultOffset($pageNumber)
    {
        return $this->getCurrentPageFirstResultOffset() + $this->getPerPage() - 1;
    }

    /**
     * @return int
     */
    public function getCurrentPageLastResultOffset()
    {
        return $this->calculateLastResultOffset($this->currentPage);
    }


    /**
     * @return array
     */
    public function getCurrentPageResults()
    {
        return (array)$this->getIterator();
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getNextPage()
    {
        if (false === $this->hasNextPage()) {
            throw new \Exception('Out of bounds');
        }

        return $this->currentPage + 1;
    }

    /**
     * @return bool
     */
    public function hasNextPage()
    {

        return $this->currentPage < $this->getPageCount();
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getPreviousPage()
    {
        if (false === $this->hasPreviousPage()) {
            throw new \Exception('Out of bounds');
        }

        return $this->currentPage - 1;
    }

    /**
     * @return bool
     */
    public function hasPreviousPage()
    {
        return $this->currentPage > 1;
    }

    /**
     * @return bool
     */
    public function canPaginate()
    {
        return $this->count() > $this->perPage;
    }

    /**
     * @param $pageNumber
     * @param $offset
     *
     * @return array
     */
    public function getPageOffsetRange($pageNumber, $offset)
    {
        $pageRange = range(max($pageNumber - $offset, 1), min($pageNumber + $offset, $this->getPageCount()));

        return $pageRange;
    }

    /**
     * @param $offset
     *
     * @return array
     */
    public function getCurrentPageOffsetRange($offset)
    {
        return $this->getPageOffsetRange($this->currentPage, $offset);
    }

    /**
     * @return int|number
     */
    public function getTotalResultCount()
    {
        return $this->count();
    }

    /**
     * @return int
     */
    public function getCurrentPageResultCount()
    {
        return count($this->getCurrentPageResults());
    }
}
