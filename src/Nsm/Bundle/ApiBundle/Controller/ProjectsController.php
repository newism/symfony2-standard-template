<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Hateoas\Configuration\Route;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Representation\PaginatedRepresentation;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\ApiBundle\Entity\Project;
use Nsm\Bundle\ApiBundle\Entity\ProjectRepository;
use Nsm\Bundle\ApiBundle\Form\Type\ProjectFilterType;
use Nsm\Bundle\ApiBundle\Form\Type\ProjectType;
use Nsm\Paginator\HateosPaginatorFactory;
use Nsm\Paginator\Paginator;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Project controller.
 */
class ProjectsController extends AbstractController
{

    /**
     * Browse all Project entities.
     *
     * @Get("/projects.{_format}", name="project_browse", defaults={"_format"="~"})
     *
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Project count limit")
     * @ApiDoc(
     *  resource=true,
     *  filters={
     *      {"name"="title", "dataType"="string"},
     *      {"name"="orderBy", "dataType"="string", "pattern"="(title|createdAt) ASC|DESC"}
     *  })
     */
    public function browseAction(Request $request, $page, $perPage)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var ProjectRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Project');

        /** @var Form $form */
        $projectSearchForm = $this->createForm(
            new ProjectFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('project_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $projectSearchForm->handleRequest($request);
        $criteria = $repo->sanatiseCriteria($projectSearchForm->getData());

        $qb = $repo->filter($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);
        $results = $pager->getCurrentPageResults();
        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $projectSearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('project_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Get("/projects/{id}.{_format}", name="project_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"project_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Project"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('Project', $id);
        $entity->getTasks();

        return $entity;
    }

    /**
     * Edits an existing Project entity.
     *
     * @Patch("/projects/{id}", name="project_patch")
     * @Get("/projects/{id}/edit", name="project_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\ProjectType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Project"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('Project', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new ProjectType(),
            $entity,
            array(
                'action' => $this->generateUrl('project_patch', array('id' => $entity->getId())),
                'method' => 'PATCH'
            )
        )->add('Update', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl(
                    'project_read',
                    array(
                        'id' => $entity->getId()
                    )
                )
            );
        }

        $responseData = array(
            'entity' => $entity,
            'form' => $form
        );

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Creates a add Project entity.
     *
     * @Post("/projects", name="project_post")
     * @Get("/projects/add", name="project_add")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\ProjectType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Project"
     * )
     */
    public function addAction(Request $request)
    {
        $entity = new Project();

        /** @var Form $form */
        $form = $this->createForm(
            new ProjectType(),
            $entity,
            array(
                'action' => $this->generateUrl('project_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('project_read', array('id' => $entity->getId())),
                Codes::HTTP_CREATED
            );
        }

        $responseData = array(
            'entity' => $entity,
            'form'   => $form,
        );

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'add')));

        return $view;

    }

    /**
     * Destroys a Project entity.
     *
     * @Delete("/projects/{id}", name="project_delete")
     * @Get("/projects/{id}/destroy", name="project_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('Project', $id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('project_destroy', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('project_browse', array()), Codes::HTTP_OK);
            } else {
                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form
        );

    }
}
