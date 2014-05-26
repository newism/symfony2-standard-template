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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\ApiBundle\Entity\Feature;
use Nsm\Bundle\ApiBundle\Entity\FeatureRepository;
use Nsm\Bundle\ApiBundle\Entity\Task;
use Nsm\Bundle\ApiBundle\Form\Type\FeatureFilterType;
use Nsm\Bundle\ApiBundle\Form\Type\FeatureType;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Feature controller.
 */
class FeaturesController extends AbstractController
{
    protected $templateGroup = 'NsmApiBundle:Features';

    /**
     * @param $id
     *
     * @return mixed
     */
    private function findFeatureOr404($id)
    {
        $entity = $this->get('nsm_api.entity.feature_repository')->find($id);

        if (!$entity instanceof Feature) {
            throw new NotFoundHttpException('Task List not found.');
        }

        return $entity;
    }

    /**
     * Browse all Feature entities.
     *
     * @Get("/features.{_format}", name="feature_browse", defaults={"_format"="~"})
     *
     * @View(templateVar="entities", serializerGroups={"feature_browse"})
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Feature count limit")
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
        $featureSearchForm = $this->createForm(
            new FeatureFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('feature_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $featureSearchForm->handleRequest($request);
        $criteria = $featureSearchForm->getData();

        $qb = $this->get('nsm_api.entity.feature_repository')->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);

        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $featureSearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('feature_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }


    /**
     * Finds and displays a Feature entity.
     *
     * @Get("/features/{id}.{_format}", name="feature_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"feature_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Feature"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findFeatureOr404($id);

        return $entity;
    }

    /**
     * Edits an existing Feature entity.
     *
     * @Patch("/features/{id}", name="feature_patch")
     * @Get("/features/{id}/edit", name="feature_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\FeatureType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Feature"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findFeatureOr404($id);

        /** @var Form $form */
        $form = $this->createForm(
            new FeatureType(),
            $entity,
            array(
                'action' => $this->generateUrl('feature_patch', array('id' => $entity->getId())),
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
                    'feature_read',
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
     * Creates a add Feature entity.
     *
     * @Post("/features", name="feature_post")
     * @Get("/features/add", name="feature_add")
     *
     * @QueryParam(name="projectId", requirements="\d+", strict=true, nullable=true, description="The tasks project")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\FeatureType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Feature"
     * )
     */
    public function addAction(Request $request, $projectId)
    {
        $entity = new Feature();

        if (null !== $projectId) {
            $project = $this->get('nsm_api.entity.project_repository')->find($projectId);
            $entity->setProject($project);
        }

        $entity->addTask(new Task());


        /** @var Form $form */
        $form = $this->createForm(
            new FeatureType(),
            $entity,
            array(
                'action' => $this->generateUrl('feature_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');


        $form->handleRequest($request);

        if ($form->get('Save')->isClicked() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('feature_read', array('id' => $entity->getId())),
                Codes::HTTP_CREATED
            );
        }

        return array(
            'entity' => $entity,
            'form' => $form,
        );
    }

    /**
     * Deletes a Feature entity.
     *
     * @Delete("/features/{id}", name="feature_delete")
     * @Get("/features/{id}/destroy", name="feature_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findFeatureOr404($id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('feature_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('feature_browse', array()), Codes::HTTP_OK);
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
