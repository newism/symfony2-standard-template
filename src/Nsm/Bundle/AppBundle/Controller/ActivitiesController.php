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
use Nsm\Bundle\AppBundle\Entity\Activity;
use Nsm\Bundle\AppBundle\Entity\ActivityRepository;
use Nsm\Bundle\AppBundle\Form\Type\ActivityFilterType;
use Nsm\Bundle\AppBundle\Form\Type\ActivityType;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Activity controller.
 */
class ActivitiesController extends AbstractController
{
    protected $templateGroup = 'NsmAppBundle:Activities';

    /**
     * @param $id
     *
     * @return Activity
     */
    private function findActivityOr404($id)
    {
        $entity = $this->get('nsm_app.entity.activity_repository')->find($id);

        if (!$entity instanceof Activity) {
            throw new NotFoundHttpException('Activity not found.');
        }

        return $entity;
    }

    /**
     * Browse all Activity entities.
     *
     * @Get("/activities.{_format}", name="activity_browse", defaults={"_format"="~"})
     *
     * @View(templateVar="entities", serializerGroups={"activity_browse"})
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Activity count limit")
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
    public function browseAction(Request $request, $page, $perPage)
    {
        /** @var Form $form */
        $activitySearchForm = $this->createForm(
            new ActivityFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('activity_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $activitySearchForm->handleRequest($request);
        $criteria = $activitySearchForm->getData();

        $qb = $this->get('nsm_app.entity.activity_repository')->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);

        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $activitySearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('activity_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Edits an existing Activity entity.
     *
     * @Patch("/activities/{id}", name="activity_patch")
     * @Get("/activities/{id}/edit", name="activity_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\AppBundle\Form\Type\ActivityType",
     *  output="Nsm\Bundle\AppBundle\Entity\Activity"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findActivityOr404($id);

        /** @var Form $form */
        $form = $this->createForm(
            new ActivityType(),
            $entity,
            array(
                'action' => $this->generateUrl('activity_patch', array('id' => $entity->getId())),
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
                    'activity_read',
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
     * Creates a add Activity entity.
     *
     * @Post("/activities", name="activity_post")
     * @Get("/activities/add", name="activity_add")
     *
     * @View()
     * @QueryParam(name="taskId", requirements="\d+", strict=true, nullable=true, description="The activities task")
     * @ApiDoc(
     *  input="Nsm\Bundle\AppBundle\Form\Type\ActivityType",
     *  output="Nsm\Bundle\AppBundle\Entity\Activity"
     * )
     */
    public function addAction(Request $request, $taskId)
    {
        $entity = new Activity();

        if (null !== $taskId) {
            $task = $this->get('nsm_app.entity.task_repository')->find($taskId);
            $entity->setTask($task);
        }

        /** @var Form $form */
        $form = $this->createForm(
            new ActivityType(),
            $entity,
            array(
                'action' => $this->generateUrl('activity_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->start();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('activity_read', array('id' => $entity->getId())),
                Codes::HTTP_CREATED
            );
        }

        return array(
            'entity' => $entity,
            'form' => $form,
        );
    }


    /**
     * Deletes a Activity entity.
     *
     * @Delete("/activities/{id}", name="activity_delete")
     * @Get("/activities/{id}/destroy", name="activity_destroy")
     *
     * @View("NsmAppBundle:Activity:destroy.html.twig")
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findActivityOr404($id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('activity_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('activity_browse', array()), Codes::HTTP_OK);
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form
        );

    }

    /**
     * Finds and displays a Activity entity.
     *
     * @Get("/activities/{id}", name="activity_read", requirements={"id" = "\d+"})
     *
     * @View(templateVar="entity", serializerGroups={"activity_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\AppBundle\Entity\Activity"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findActivityOr404($id);

        return $entity;
    }

    /**
     * Starts the Activity time.
     *
     * @Get("/activities/{id}/start", name="activity_start")
     *
     * @View("NsmAppBundle:Activity:read.html.twig", templateVar="entity", serializerGroups={"activity_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\AppBundle\Entity\Activity"
     * )
     */
    public function startAction($id)
    {
        $entity = $this->findActivityOr404($id);
        $entity->startTimer();
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    /**
     * Stop the Activity time.
     *
     * @Get("/activities/{id}/stop", name="activity_stop")
     *
     * @View("NsmAppBundle:Activity:read.html.twig", templateVar="entity", serializerGroups={"activity_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\AppBundle\Entity\Activity"
     * )
     */
    public function stopAction($id)
    {
        $entity = $this->findActivityOr404($id);
        $entity->stopTimer();
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }
}
