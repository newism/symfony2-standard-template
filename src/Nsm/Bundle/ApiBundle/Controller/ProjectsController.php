<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations\View;

use Nsm\Bundle\ApiBundle\Entity\Project;
use Nsm\Bundle\ApiBundle\Form\ProjectType;

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
 * Project controller.
 */
class ProjectsController extends FOSRestController
{

    /**
     * Lists all Project entities.
     *
     * @Get("/projects", name="projects_index")
     *
     * @View("NsmApiBundle:Project:index.html.twig", templateVar="entities")
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Project count limit")
     * @ApiDoc(
     *  resource=true,
     *  filters={
     *      {"name"="title", "dataType"="string"},
     *      {"name"="orderBy", "dataType"="string", "pattern"="(title|createdAt) ASC|DESC"}
     *  })
     */
    public function indexAction($page, $perPage)
    {
        $em       = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('NsmApiBundle:Project')->findAll();

        return  $entities;
    }

    /**
     * Creates a new Project entity.
     *
     * @Post("/projects", name="projects_post")
     * @Get("/projects/new", name="projects_new")
     *
     * @View("NsmApiBundle:Project:new.html.twig")
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\ProjectType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Project"
     * )
     */
    public function newAction(Request $request)
    {
        $entity = new Project();
        $form = $this->createForm(
            new ProjectType(),
            $entity,
            array(
                'action' => $this->generateUrl('projects_post')
            )
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->routeRedirectView('projects_show', array('id' => $entity->getId()), Codes::HTTP_CREATED);
        }

        return array(
            'entity' => $entity,
            'form'   => $form,
        );
    }
    
    /**
     * Finds and displays a Project entity.
     *
     * @Get("/projects/{id}", name="projects_show")
     * 
     * @View("NsmApiBundle:Project:show.html.twig", templateVar="entity")
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Project"
     * )
     */
    public function showAction($id)
    {
        $entity = $this->findEntityOr404('Project', $id);

        return $entity;
    }

    /**
     * Edits an existing Project entity.
     *
     * @Patch("/projects/{id}", name="projects_patch")
     * @Get("/projects/{id}/edit", name="projects_edit")
     * 
     * @View("NsmApiBundle:Project:edit.html.twig")
     * @ApiDoc(
     *  route="projects_patch",
     *  input="Nsm\Bundle\ApiBundle\Form\ProjectType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Project"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findEntityOr404('Project', $id);

        $form = $this->createForm(
            new ProjectType(),
            $entity,
            array(
                'action' => $this->generateUrl('projects_patch', array('id' => $entity->getId())),
                'method' => 'PATCH'
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->routeRedirectView('projects_index', array(), Codes::HTTP_NO_CONTENT);
        }

        return array(
            'entity' => $entity,
            'form'   => $form
        );
    }

    /**
     * Deletes a Project entity.
     *
     * @Delete("/projects/{id}", name="projects_delete")
     * @Get("/projects/{id}/remove", name="projects_remove")
     *
     * @View("NsmApiBundle:Project:remove.html.twig")
     * @ApiDoc()
     */
    public function removeAction(Request $request, $id)
    {
        $entity = $this->findEntityOr404('Project', $id);

        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('projects_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isValid()) {

//            $em = $this->getDoctrine()->getManager();
//            $em->remove($entity);
//            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())){
                return $this->routeRedirectView('projects_index', array(), Codes::HTTP_OK);
            } else {
                return $this->view(null, Codes::HTTP_NO_CONTENT);
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form
        );

    }

    public function findEntityOr404($entityDisriminator, $id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NsmApiBundle:'.$entityDisriminator)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        return $entity;
    }
}
