<?php

namespace ClubEvo\Bundle\ApiBundle\Controller;

// Core
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

// Third Party
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Util\Codes;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

// Project Based
use ClubEvo\Bundle\ApiBundle\Controller\AbstractController;
use ClubEvo\Bundle\ApiBundle\Entity\RoadsideAssistEmployee;
use ClubEvo\Bundle\ApiBundle\Entity\RoadsideAssistEmployeeManager;
use ClubEvo\Bundle\ApiBundle\Entity\RoadsideAssistEmployeeRepository;
use ClubEvo\Bundle\ApiBundle\Entity\RoadsideAssistEmployeeQueryBuilder;
use ClubEvo\Bundle\ApiBundle\Form\Type\RoadsideAssistEmployeeType;
use ClubEvo\Bundle\ApiBundle\Form\Type\RoadsideAssistEmployeeFilterType;

use Nsm\Bundle\FormBundle\Form\Model\DateRange;

/**
 * RoadsideAssistEmployee controller.
 */
class RoadsideAssistEmployeeController extends AbstractController
{
   /**
     * Browse all Roadside Assist Employee entities.
     *
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Roadside Assist Employee count limit")
     *
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

        /** @var RoadsideAssistEmployeeRepository $repo */
        $repo = $em->getRepository('ClubEvoApiBundle:RoadsideAssistEmployee');

        /** @var Form $form */
        $roadsideAssistEmployeeSearchForm = $this->createForm(
            new RoadsideAssistEmployeeFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('roadside_assist_employee_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $roadsideAssistEmployeeSearchForm->handleRequest($request);
        $criteria = $repo->sanatiseCriteria($roadsideAssistEmployeeSearchForm->getData());

        $qb = $repo->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);
        $results = $pager->getCurrentPageResults();
        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $roadsideAssistEmployeeSearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('roadside_assist_employee_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Finds and displays a Roadside Assist Employee entity.
     *
     * @View(templateVar="entity", serializerGroups={"roadside_assist_employee_read"})
     * @ApiDoc(
     *  output="ClubEvo\Bundle\ApiBundle\Entity\RoadsideAssistEmployee"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('RoadsideAssistEmployee', $id);

        return $entity;
    }

    /**
     * Edits an existing Roadside Assist Employee entity.
     *
     * @View()
     * @ApiDoc(
     *  input="ClubEvo\Bundle\ApiBundle\Form\Type\RoadsideAssistEmployeeType",
     *  output="ClubEvo\Bundle\ApiBundle\Entity\RoadsideAssistEmployee"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('RoadsideAssistEmployee', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new RoadsideAssistEmployeeType(),
            $entity,
            array(
                'action' => $this->generateUrl('roadside_assist_employee_patch', array('id' => $entity->getId())),
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
                    'roadside_assist_employee_read',
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
     * Creates a Roadside Assist Employee entity.
     *
     * @View()
     * @ApiDoc(
     *  input="ClubEvo\Bundle\ApiBundle\Form\Type\RoadsideAssistEmployeeType",
     *  output="ClubEvo\Bundle\ApiBundle\Entity\RoadsideAssistEmployee"
     * )
     */
    public function addAction(Request $request)
    {
        $entity = new RoadsideAssistEmployee();

        /** @var Form $form */
        $form = $this->createForm(
            new RoadsideAssistEmployeeType(),
            $entity,
            array(
                'action' => $this->generateUrl('roadside_assist_employee_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('roadside_assist_employee_read', array('id' => $entity->getId())),
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
     * Destroys a Roadside Assist Employee entity.
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('RoadsideAssistEmployee', $id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('roadside_assist_employee_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('roadside_assist_employee_browse', array()));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form
        );

    }
}
