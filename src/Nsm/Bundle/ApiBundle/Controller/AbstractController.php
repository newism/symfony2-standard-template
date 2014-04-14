<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nsm\Bundle\ApiBundle\Entity\AbstractManager;

/**
 * Abstract controller.
 */
class AbstractController extends FOSRestController
{
    public $entityDiscriminator;
    public $entityManager;

    /**
     * Get Entity Discriminator based on controller name
     *
     * @return mixed
     */
    public function getEntityDiscriminator()
    {
        if (null === $this->entityDiscriminator) {
            $class = explode("\\", get_called_class());
            $class = end($class);
            $class = substr($class, 0, -10);
            $this->entityDiscriminator = Inflector::singularize($class);
        }

        return $this->entityDiscriminator;
    }

    /**
     * Get the entity manager for the controller
     * 
     * @return AbstractManager
     */
    public function getEntityManager(){

        if (null === $this->entityManager) {
            $entityDiscriminator = $this->getEntityDiscriminator();
            $this->entityManager = $this->get(sprintf('%s.manager', $entityDiscriminator));
        }

        return $this->entityManager;
    }
    
    /**
     * Find an entity by ID or return null
     *
     * @param $entityDisriminator
     * @param $id
     *
     * @return null
     */
    public function find($entityDisriminator, $id)
    {
        if (true === empty($id)) {
            return null;
        }

        $em     = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NsmApiBundle:' . $entityDisriminator)->find($id);

        return $entity;
    }

    /**
     * Find an entity by ID or throw a 404
     *
     * @param $entityDisriminator
     * @param $id
     *
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function findOr404($entityDisriminator, $id)
    {
        $entity = $this->find($entityDisriminator, $id);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find %s entity.', $entityDisriminator));
        }

        return $entity;
    }

    /**
     * Paginate a Query
     *
     * @param QueryBuilder $qb
     * @param null         $perPage
     * @param null         $page
     *
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
     * @param $template
     *
     * @return string
     */
    public function getTemplate($template)
    {
        // Todo: Refactor this into config or something
        $class = explode("\\", get_called_class());
        $class = end($class);
        $class = substr($class, 0, -10);

        $templatePath = sprintf("NsmApiBundle:%s:%s.html.twig", $class, $template);

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
}
