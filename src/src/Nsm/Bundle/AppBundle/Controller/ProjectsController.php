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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\AppBundle\Entity\Project;
use Nsm\Bundle\AppBundle\Entity\Task;
use Nsm\Bundle\AppBundle\Form\Type\ProjectFilterType;
use Nsm\Bundle\AppBundle\Form\Type\ProjectType;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Nsm\Bundle\FormBundle\Form\Type\OrderByType;
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
     * @View(templateVar="entities", serializerGroups={"project_browse"})
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Project count limit")
     * @QueryParam(name="orderBy", array=true, default={"id"="asc"})
     * @ApiDoc(
     *  resource=true,
     *  filters={
     *      {"name"="title", "dataType"="string"},
     *      {"name"="orderBy", "dataType"="string", "pattern"="(title|createdAt) ASC|DESC"}
     *  })
     */
    public function browseAction(Request $request, $page, $perPage, $orderBy)
    {
        /** @var Form $form */
//        $projectSearchForm = $this->createFormBuilder(array(
//            'page' => 1,
//            'perPage' => 50,
//            'orderBy' => array(
//                'id' => 'desc'
//            ),
//            'filter' => array()
//        ))
//        ->add('page', 'number')
//        ->add('perPage', 'number')
//        ->add('orderBy', new OrderByType(), array(
//                'attributes' => array(
//                    'id',
//                    'title'
//                )
//            ))
//        ->add('filter', new ProjectFilterType())
//        ->getForm();


        $projectSearchForm = $this->createForm(
            new ProjectFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('project_browse'),
                'method' => 'GET',
                'layout' => 'table'
            )
        )->add(
            'search',
            'submit',
            array(
                'attr' => array(
                    'class' => 'Button Button--default'
                )
            )
        );

        $projectSearchForm->handleRequest($request);
        $criteria = $projectSearchForm->getData();

        $qb = $this->get('nsm_app.entity.project_repository')->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);

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
                    $this->generateUrl('project_read', array('id' => $entity->getId()));
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

    /**
     * Destroys a Project entity.
     *
     * @Delete("/projects/{id}", name="project_delete")
     * @Get("/projects/{id}/destroy", name="project_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function loadActionsAction(Request $request, $id)
    {
    }
}
