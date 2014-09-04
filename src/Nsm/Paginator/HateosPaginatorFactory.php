<?php

namespace Nsm\Paginator;

use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class HateosPaginatorFactory {

    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * @var string
     */
    private $limitParameterName;

    /**
     * @param null $pageParameterName
     * @param null $limitParameterName
     */
    public function __construct($pageParameterName = null, $limitParameterName = null)
    {
        $this->pageParameterName  = $pageParameterName;
        $this->limitParameterName = $limitParameterName;
    }

    /**
     * @param Paginator $pager
     * @param Route     $route
     * @param null      $inline
     *
     * @return PaginatedRepresentation
     */
    public function createRepresentation(Paginator $pager, Route $route, $inline = null)
    {
        if (null === $inline) {
            $inline = new CollectionRepresentation($pager->getCurrentPageResults());
        }

        return new PaginatedRepresentation(
            $inline,
            $route->getName(),
            $route->getParameters(),
            $pager->getCurrentPage(),
            $pager->getPerPage(),
            $pager->getPageCount(),
            $this->getPageParameterName(),
            $this->getLimitParameterName(),
            $route->isAbsolute(),
            $pager->getTotalResultCount()
        );
    }

    /**
     * @return string
     */
    public function getPageParameterName()
    {
        return $this->pageParameterName;
    }

    /**
     * @return string
     */
    public function getLimitParameterName()
    {
        return $this->limitParameterName;
    }
}

