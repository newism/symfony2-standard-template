<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Representation\PaginatedRepresentation;
use Nsm\Bundle\ApiBundle\Entity\PersonQueryBuilder;
use Nsm\Bundle\ApiBundle\Form\Type\PersonFilterType;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;

use Nsm\Bundle\ApiBundle\Entity\Person;
use Nsm\Bundle\ApiBundle\Form\Type\PersonType;
use Nsm\Bundle\ApiBundle\Entity\PersonRepository;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;

/**
 * People controller.
 */
class PeopleController extends AbstractController
{
    /**
     * Browse all Person entities.
     *
     * @Get("/people.{_format}", name="people_browse", defaults={"_format"="~"})
     *
     * @View(templateVar="entities", serializerGroups={"people_browse"})
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Person count limit")
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
        /** @var PersonRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Person');

        /** @var Form $form */
        $taskSearchForm = $this->createForm(
            new PersonFilterType(),
            null,
            array(
                'action' => $this->generateUrl('people_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $taskSearchForm->submit($request);
        $criteria = $repo->sanatiseCriteria($taskSearchForm->getData());

        $qb = $repo->filter($criteria);
        $pager = $this->paginateQuery($qb, $perPage, $page);

        $responseData = array();

        if(true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $taskSearchForm->createView();
        } else {

            $paginatedCollection = new PaginatedRepresentation(
                new CollectionRepresentation(
                    (array)$pager->getCurrentPageResults(),
                    'people', // embedded rel
                    'people' // xml element name
                ),
                'people_browse', // route
                array(), // route parameters
                $pager->getCurrentPage(),
                $pager->getMaxPerPage(),
                $pager->getNbPages()
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }


    /**
     * Finds and displays a Person entity.
     *
     * @Get("/people/{id}.{_format}", name="people_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"task_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Person"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('Person', $id);

        return $entity;
    }

    /**
     * Edits an existing Person entity.
     *
     * @Patch("/people/{id}", name="people_patch")
     * @Get("/people/{id}/edit", name="people_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\PersonType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Person"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('Person', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new PersonType(),
            $entity,
            array(
                'action' => $this->generateUrl('people_patch', array('id' => $entity->getId())),
                'method' => 'PATCH'
            )
        )->add('Update', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('people_browse', array()), Codes::HTTP_NO_CONTENT);
        }

        return array(
            'entity' => $entity,
            'form'   => $form
        );
    }



    /**
     * Creates a add Person entity.
     *
     * @Post("/people", name="people_post")
     * @Get("/people/add", name="people_add")
     *
     * @QueryParam(name="projectId", requirements="\d+", strict=true, nullable=true, description="The people project")
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\PersonType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Person"
     * )
     */
    public function addAction(Request $request, $projectId = null)
    {
        $entity = new Person();

        $project = $this->find('Project', $projectId);
        $entity->setProject($project);

        /** @var Form $form */
        $form = $this->createForm(
            new PersonType(),
            $entity,
            array(
                'action' => $this->generateUrl('people_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('people_read', array('id' => $entity->getId())), Codes::HTTP_CREATED);
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
     * Deletes a Person entity.
     *
     * @Delete("/people/{id}", name="people_delete")
     * @Get("/people/{id}/destroy", name="people_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('Person', $id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('people_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('people_browse', array()), Codes::HTTP_OK);
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
