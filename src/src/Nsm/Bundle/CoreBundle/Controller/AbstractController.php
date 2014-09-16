<?php

namespace Nsm\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Controller\FOSRestController;
use Nsm\DoctrinePaginator\DoctrinePaginatorDecorator;
use Nsm\DoctrinePaginator\HateosPaginatorFactory;

/**
 * Abstract controller.
 */
class AbstractController extends FOSRestController
{
    protected $templateGroup;


    /**
     * @param $template
     *
     * @return string
     */
    public function getTemplate($template)
    {
        $templatePath = sprintf("%s:%s.html.twig", $this->templateGroup, $template);

        return $templatePath;
    }

    /**
     * Get the view handler
     *
     * @return \FOS\RestBundle\View\ViewHandler
     */
    public function getViewHandler()
    {
        return $this->get('fos_rest.view_handler');
    }

    /**
     * Paginate a Query
     *
     * @param QueryBuilder $qb
     * @param null         $perPage
     * @param null         $pageNo
     *
     * @return Paginator
     */
    public function paginateQuery(QueryBuilder $qb, $perPage = null, $pageNo = null)
    {
        $paginator = new Paginator($qb, false);

        return new DoctrinePaginatorDecorator($paginator, (int) $perPage, (int) $pageNo);
    }

    /**
     * @param Paginator $pager
     * @param           $route
     *
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function createPaginatedCollection(DoctrinePaginatorDecorator $pager, $route)
    {
        $pagerFactory = new HateosPaginatorFactory();
        $paginatedCollection = $pagerFactory->createRepresentation(
            $pager,
            $route
        );

        return $paginatedCollection;
    }
}
