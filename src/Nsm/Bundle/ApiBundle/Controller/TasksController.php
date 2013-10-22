<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Nsm\Bundle\ApiBundle\Entity\TaskQueryBuilder;
use Nsm\Bundle\ApiBundle\Form\Type\TaskFilterType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

use Nsm\Bundle\ApiBundle\Entity\Task;
use Nsm\Bundle\ApiBundle\Form\Type\TaskType;
use Nsm\Bundle\ApiBundle\Entity\TaskRepository;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\Rest\Util\Codes;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;

/**
 * Task controller.
 */
class TasksController extends AbstractController
{
    /**
     * Lists all Task entities.
     *
     * @Get("/tasks", name="tasks_index")
     *
     * @View("NsmApiBundle:Task:index.html.twig", templateVar="entities", serializerGroups={"task_list"})
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
    public function indexAction(Request $request, $page, $perPage)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var TaskRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Task');

        /** @var Form $form */
        $taskSearchForm = $this->createForm(
            new TaskFilterType(),
            null,
            array(
                'action' => $this->generateUrl('tasks_index'),
                'method' => 'GET'
            )
        );

        $taskSearchForm->submit($request);
        $criteria = $repo->sanatiseCriteria($taskSearchForm->getData());

        $qb = $repo->filter($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);

        $this->get('fsc_hateoas.metadata.relations_manager')->addBasicRelations($pager);

        return array(
            'search_form' => $taskSearchForm->createView(),
            "pager" => $pager
        );
    }

    /**
     * Creates a new Task entity.
     *
     * @Post("/tasks", name="tasks_post")
     * @Get("/tasks/new", name="tasks_new")
     *
     * @View("NsmApiBundle:Task:new.html.twig")
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\TaskType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Task"
     * )
     */
    public function newAction(Request $request)
    {
        $entity = new Task();

        /** @var Form $form */
        $form = $this->createForm(
            new TaskType(),
            $entity,
            array(
                'action' => $this->generateUrl('tasks_post'),
                'method' => 'POST'
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->routeRedirectView('tasks_show', array('id' => $entity->getId()), Codes::HTTP_CREATED);
        }

        return array(
            'entity' => $entity,
            'form'   => $form,
        );
    }

    /**
     * Finds and displays a Task entity.
     *
     * @Get("/tasks/{id}", name="tasks_show")
     *
     * @View("NsmApiBundle:Task:show.html.twig", templateVar="entity", serializerGroups={"task_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Task"
     * )
     */
    public function showAction($id)
    {
        $entity = $this->findEntityOr404('Task', $id);

        return $entity;
    }

    /**
     * Edits an existing Task entity.
     *
     * @Patch("/tasks/{id}", name="tasks_patch")
     * @Get("/tasks/{id}/edit", name="tasks_edit")
     *
     * @View("NsmApiBundle:Task:edit.html.twig")
     * @ApiDoc(
     *  route="tasks_patch",
     *  input="Nsm\Bundle\ApiBundle\Form\Type\TaskType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Task"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findEntityOr404('Task', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new TaskType(),
            $entity,
            array(
                'action' => $this->generateUrl('tasks_patch', array('id' => $entity->getId())),
                'method' => 'PATCH'
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->routeRedirectView('tasks_index', array(), Codes::HTTP_NO_CONTENT);
        }

        return array(
            'entity' => $entity,
            'form'   => $form
        );
    }

    /**
     * Deletes a Task entity.
     *
     * @Delete("/tasks/{id}", name="tasks_delete")
     * @Get("/tasks/{id}/remove", name="tasks_remove")
     *
     * @View("NsmApiBundle:Task:remove.html.twig")
     * @ApiDoc()
     */
    public function removeAction(Request $request, $id)
    {
        $entity = $this->findEntityOr404('Task', $id);

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
                return $this->routeRedirectView('tasks_index', array(), Codes::HTTP_OK);
            } else {
                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form
        );

    }
}
