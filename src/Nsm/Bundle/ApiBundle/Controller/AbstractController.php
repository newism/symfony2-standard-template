<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstract controller.
 */
class AbstractController extends FOSRestController
{
    /**
     * Find an entity by ID or throw a 404
     *
     * @param $entityDisriminator
     * @param $id
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findEntityOr404($entityDisriminator, $id)
    {
        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NsmApiBundle:' . $entityDisriminator)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Abstract entity.');
        }

        return $entity;
    }

    /**
     * Paginate a Query
     *
     * @param QueryBuilder $qb
     * @param null $perPage
     * @param null $page
     * @return Pagerfanta
     */
    public function paginateQuery(QueryBuilder $qb, $perPage = null, $page = null)
    {
        $adaptor    = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adaptor);

        if (null !== $perPage) {
            $pagerfanta->setMaxPerPage($perPage);
        }

        if (null !== $page) {
            $pagerfanta->setCurrentPage($page);
        }

        return $pagerfanta;
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
}
