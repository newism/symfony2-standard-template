<?php

namespace Nsm\Bundle\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Nsm\Bundle\CoreBundle\Entity\AbstractManager;
use Nsm\Paginator\HateosPaginatorFactory;
use Nsm\Paginator\Paginator;

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
     * @param null         $page
     *
     * @return Paginator
     */
    public function paginateQuery(QueryBuilder $qb, $perPage = null, $page = null)
    {
        $paginator = new Paginator($qb, false);

        if (null !== $perPage) {
            $paginator->setPerPage($perPage);
        }

        if (null !== $page) {
            $paginator->setCurrentPage($page);
        }

        return $paginator;
    }

    /**
     * @param $pager
     * @param $route
     *
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function createPaginatedCollection($pager, $route)
    {
        $pagerFactory = new HateosPaginatorFactory();
        $paginatedCollection = $pagerFactory->createRepresentation(
            $pager,
            $route
        );

        return $paginatedCollection;
    }
}
