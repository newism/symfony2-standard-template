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
use Nsm\Bundle\ApiBundle\Entity\Task;
use Nsm\Bundle\ApiBundle\Entity\TaskRepository;
use Nsm\Bundle\ApiBundle\Form\Type\TaskFilterType;
use Nsm\Bundle\ApiBundle\Form\Type\TaskType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Task controller.
 */
class TasksController extends AbstractController
{
    /**
     * Browse all Task entities.
     *
     * @Get("/tasks.{_format}", name="tasks_browse", defaults={"_format"="~"})
     *
     * @View(templateVar="entities", serializerGroups={"task_browse"})
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Task count limit")
     * @ApiDoc(
     *  resource=true,
     *  filters={
     *      {"name"="title", "dataType"="string"},
     *      {"name"="project", "dataType"="integer"},
     *      {"name"="page", "dataType"="integer"},
     *      {"name"="perPage", "dataType"="integer"},
     *      {"name"="orderBy", "dataType"="string", "pattern"="(title|createdAt) ASC|DESC"}
     *  })
     */
    public function browseAction(Request $request, $page, $perPage)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TaskRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Task');

        $projects = $this->get('project.repository')->findAll();

        /** @var Form $form */
        $taskSearchForm = $this->createForm(
            new TaskFilterType(),
            array(
                'project' => $projects
            ),
            array(
                'action' => $this->generateUrl('tasks_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $taskSearchForm->handleRequest($request);
        $criteria = $repo->sanatiseCriteria($taskSearchForm->getData());

        $qb = $repo->filter($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);
        $results = $pager->getCurrentPageResults();
        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $taskSearchForm->createView();
        } else {

            $pagerfantaFactory   = new PagerfantaFactory();
            $paginatedCollection = $pagerfantaFactory->createRepresentation(
                $pager,
                new Route('tasks_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }


    /**
     * Finds and displays a Task entity.
     *
     * @Get("/tasks/{id}.{_format}", name="tasks_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"task_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Task"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('Task', $id);

        return $entity;
    }

    /**
     * Edits an existing Task entity.
     *
     * @Patch("/tasks/{id}", name="tasks_patch")
     * @Get("/tasks/{id}/edit", name="tasks_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\TaskType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Task"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('Task', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new TaskType(),
            $entity,
            array(
                'action' => $this->generateUrl('tasks_patch', array('id' => $entity->getId())),
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
                    'tasks_read',
                    array(
                        'id' => $entity->getId()
                    )
                )
            );
        }

        return array(
            'entity' => $entity,
            'form' => $form
        );
    }


/**
 * Creates a add Task entity.
 *
 * @Post("/tasks", name="tasks_post")
 * @Get("/tasks/add", name="tasks_add")
 *
 * @QueryParam(name="projectId", requirements="\d+", strict=true, nullable=true, description="The tasks project")
 *
 * @View()
 * @ApiDoc(
 *  input="Nsm\Bundle\ApiBundle\Form\Type\TaskType",
 *  output="Nsm\Bundle\ApiBundle\Entity\Task"
 * )
 */
public function addAction(Request $request, $projectId)
{
    $entity = new Task();

    $project = $this->find('Project', $projectId);
    $entity->setProject($project);

    /** @var Form $form */
    $form = $this->createForm(
        new TaskType(),
        $entity,
        array(
            'action' => $this->generateUrl('tasks_post'),
            'method' => 'POST'
        )
    )->add('Save', 'submit');

    $form->handleRequest($request);

    if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $this->redirect(
            $this->generateUrl('tasks_read', array('id' => $entity->getId())),
            Codes::HTTP_CREATED
        );
    }

    return array(
        'entity' => $entity,
        'form' => $form,
    );
}

    /**
     * Deletes a Task entity.
     *
     * @Delete("/tasks/{id}", name="tasks_delete")
     * @Get("/tasks/{id}/destroy", name="tasks_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('Task', $id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('tasks_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('tasks_browse', array()), Codes::HTTP_OK);
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
