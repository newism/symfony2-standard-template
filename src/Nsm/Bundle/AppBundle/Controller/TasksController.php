<?php

namespace Nsm\Bundle\AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Hateoas\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\AppBundle\Entity\Task;
use Nsm\Bundle\AppBundle\Entity\TaskRepository;
use Nsm\Bundle\AppBundle\Form\Type\TaskFilterType;
use Nsm\Bundle\AppBundle\Form\Type\TaskType;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Nsm\Bundle\AppBundle\Entity\TaskQueryBuilder;

/**
 * Task controller.
 */
class TasksController extends AbstractController
{
    protected $templateGroup = 'NsmAppBundle:Tasks';

    /**
     * @param $id
     *
     * @return mixed
     */
    private function findTaskOr404($id)
    {
        $entity = $this->get('nsm_app.entity.task_repository')->find($id);

        if (!$entity instanceof Task) {
            throw new NotFoundHttpException('Task not found.');
        }

        return $entity;
    }

    /**
     * Browse all Task entities.
     *
     * @Get(
     *      "/tasks.{_format}",
     *      name = "task_browse",
     *      defaults = {
     *          "_format" = "~"
     *      }
     * )
     *
     * @View(templateVar="entities", serializerGroups={"task_browse", "Default"})
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Task count limit")
     * @QueryParam(name="orderBy", array=true, default={"id"="asc"})
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
    public function browseAction(Request $request, $page, $perPage, $orderBy, $_format)
    {
        /** @var Form $form */
        $taskSearchForm = $this->createForm(
            new TaskFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('task_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $taskSearchForm->handleRequest($request);
        $criteria = $taskSearchForm->getData();

        /** @var TaskQueryBuilder $qb */
        $qb = $this->get('nsm_app.entity.task_repository')->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);

        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $taskSearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('task_browse', array())
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
     * @Get("/tasks/{id}.{_format}", name="task_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"task_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\AppBundle\Entity\Task"
     * )
     */
    public function readAction(Request $request, $id)
    {
        $entity = $this->findTaskOr404($id);

        $view = $this->view($entity);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;

    }

    /**
     * Edits an existing Task entity.
     *
     * @Patch("/tasks/{id}", name="task_patch")
     * @Get("/tasks/{id}/edit", name="task_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\AppBundle\Form\Type\TaskType",
     *  output="Nsm\Bundle\AppBundle\Entity\Task"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findTaskOr404($id);

        /** @var Form $form */
        $form = $this->createForm(
            new TaskType(),
            $entity,
            array(
                'action' => $this->generateUrl('task_patch', array('id' => $entity->getId())),
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
                    'task_read',
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
     * @Post("/tasks", name="task_post")
     * @Get("/tasks/add", name="task_add")
     *
     * @QueryParam(name="projectId", requirements="\d+", strict=true, nullable=true, description="The tasks project")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\AppBundle\Form\Type\TaskType",
     *  output="Nsm\Bundle\AppBundle\Entity\Task"
     * )
     */
    public function addAction(Request $request, $projectId)
    {
        $entity = new Task();

        if (null !== $projectId) {
            $project = $this->get('nsm_app.entity.project_repository')->find($projectId);
            $entity->setProject($project);
        }

        /** @var Form $form */
        $form = $this->createForm(
            new TaskType(),
            $entity,
            array(
                'action' => $this->generateUrl('task_post'),
                'method' => 'POST'
            )
        )
            ->add('Save', 'submit')
            ->add('Save and add another', 'submit')
            ->add(
                'Refresh',
                'submit',
                array(
                    'attr' => array(
                        'formnovalidate' => 'formnovalidate'
                    ),
                    'validation_groups' => false
                )
            );

        $form->handleRequest($request);

        if (
            ($form->get('Save')->isClicked() || $form->get('Save and add another')->isClicked())
            && $form->isValid()
        ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $targetPath = ($form->get('Save and add another')->isClicked())
                            ? $this->generateUrl('task_add', array('projectId' => $entity->getProject()->getId()))
                            : $this->generateUrl('task_read', array('id' => $entity->getId()));

            return $this->redirect(
                $targetPath,
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
     * @Delete("/tasks/{id}", name="task_delete")
     * @Get("/tasks/{id}/destroy", name="task_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findTaskOr404($id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('task_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('task_browse', array()), Codes::HTTP_OK);
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
