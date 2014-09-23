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
use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\AppBundle\Entity\Project;
use Nsm\Bundle\AppBundle\Entity\Task;
use Nsm\Bundle\AppBundle\Form\Type\ProjectFilterType;
use Nsm\Bundle\AppBundle\Form\Type\ProjectType;
use Nsm\Bundle\AppBundle\Form\Type\TestType;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Project controller.
 */
class ProjectsController extends AbstractController
{
    protected $templateGroup = 'NsmAppBundle:Projects';

    /**
     * @param $id
     *
     * @return mixed
     */
    private function findProjectOr404($id)
    {
        $entity = $this->get('nsm_app.entity.project_repository')->find($id);

        if (!$entity instanceof Project) {
            throw new NotFoundHttpException('Project not found.');
        }

        return $entity;
    }

    /**
     * Browse all Project entities.
     *
     * @Get("/projects.{_format}", name="project_browse", defaults={"_format"="~"})
     *
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="2", strict=true, description="Project count limit")
     * @QueryParam(name="orderBy", array=true, default={"id"="asc"})
     * @ApiDoc(
     *  resource=true,
     *  filters={
     *      {"name"="title", "dataType"="string"},
     *      {"name"="orderBy", "dataType"="string", "pattern"="(title|createdAt) ASC|DESC"}
     *  })
     */
    public function browseAction(Request $request, $page, $perPage)
    {

        $projectSearchForm = $this->createForm(
            new ProjectFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('project_browse'),
                'method' => 'GET'
            )
        )->add(
            'search',
            'submit',
            array(
                'attr' => array(
                    'class' => 'Button Button--medium Button--default Button--bordered'
                )
            )
        );

        $projectSearchForm->handleRequest($request);
        $criteria = $projectSearchForm->getData();

        $qb = $this->get('nsm_app.entity.project_repository')->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);

        $view = $this->view();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {

            $templateData = array();
            $templateData['pager'] = $pager;
            $templateData['searchForm'] = $projectSearchForm->createView();
            $view->setData($templateData);

            $template = $request->query->get('_template', $this->getTemplate('browse'));
            $view->setTemplate($template);

        } else {

            $serializationGroups = $request->query->get("_serialization_groups", array("project_browse"));
            $serializationContext = SerializationContext::create();
            $serializationContext->setGroups($serializationGroups);
            $serializationContext->setSerializeNull(true);

            $view->setSerializationContext($serializationContext);

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('project_browse', array())
            );

            $view->setData($paginatedCollection);
        }

        return $view;
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Get("/projects/{id}.{_format}", name="project_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"project_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\AppBundle\Entity\Project"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findProjectOr404($id);
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
     *  input="Nsm\Bundle\AppBundle\Form\Type\ProjectType",
     *  output="Nsm\Bundle\AppBundle\Entity\Project"
     * )
     */
    public function editAction(Request $request, $id)
    {
        /** @var Project $entity */
        $entity = $this->findProjectOr404($id);

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

        $template = $request->query->has('_template') ? $request->query->get('_template') : $this->getTemplate('edit');

        $view = $this->view($responseData);
        $view->setTemplate($template);

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
     *  input="Nsm\Bundle\AppBundle\Form\Type\ProjectType",
     *  output="Nsm\Bundle\AppBundle\Entity\Project"
     * )
     */
    public function addAction(Request $request)
    {
        $entity = new Project();
        $entity->addTask(new Task());

        /** @var Form $form */
        $form = $this->createForm(
            new ProjectType(),
            $entity,
            array(
                'action' => $this->generateUrl('project_post'),
                'method' => 'POST'
            )
        )
            ->add(
                'actions',
                'button_group',
                array(
                    'required' => true,
                    'mapped' => false
                )
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            switch($form->get('actions')->get('action')->getData()) {
                case 'save':
                    $targetPath = $this->generateUrl('project_read', array('id' => $entity->getId()));
                    break;
                case 'save_add_another':
                    $targetPath = $this->generateUrl('project_add');
                    break;
                default:
                    throw new \Exception('Cannot determine action');
            }

            return $this->redirect(
                $targetPath,
                Codes::HTTP_CREATED
            );
        }

        $responseData = array(
            'entity' => $entity,
            'form' => $form,
        );

        $template = $request->query->has('_template') ? $request->query->get('_template') : $this->getTemplate('add');

        $view = $this->view($responseData);
        $view->setTemplate($template);

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
        $entity = $this->findProjectOr404($id);

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
                return $this->redirect($this->generateUrl('project_browse', array()), Codes::HTTP_SEE_OTHER);
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
