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
use ClubEvo\Bundle\ApiBundle\Entity\ServiceCenterGroupEmployee;
use ClubEvo\Bundle\ApiBundle\Entity\ServiceCenterGroupEmployeeManager;
use ClubEvo\Bundle\ApiBundle\Entity\ServiceCenterGroupEmployeeRepository;
use ClubEvo\Bundle\ApiBundle\Entity\ServiceCenterGroupEmployeeQueryBuilder;
use ClubEvo\Bundle\ApiBundle\Form\Type\ServiceCenterGroupEmployeeType;
use ClubEvo\Bundle\ApiBundle\Form\Type\ServiceCenterGroupEmployeeFilterType;

use Nsm\Bundle\FormBundle\Form\Model\DateRange;

/**
 * ServiceCenterGroupEmployee controller.
 */
class ServiceCenterGroupEmployeeController extends AbstractController
{
   /**
     * Browse all Service Center Group Employee entities.
     *
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Service Center Group Employee count limit")
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

        /** @var ServiceCenterGroupEmployeeRepository $repo */
        $repo = $em->getRepository('ClubEvoApiBundle:ServiceCenterGroupEmployee');

        /** @var Form $form */
        $serviceCenterGroupEmployeeSearchForm = $this->createForm(
            new ServiceCenterGroupEmployeeFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('service_center_group_employee_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $serviceCenterGroupEmployeeSearchForm->handleRequest($request);
        $criteria = $repo->sanatiseCriteria($serviceCenterGroupEmployeeSearchForm->getData());

        $qb = $repo->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);
        $results = $pager->getCurrentPageResults();
        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $serviceCenterGroupEmployeeSearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('service_center_group_employee_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Finds and displays a Service Center Group Employee entity.
     *
     * @View(templateVar="entity", serializerGroups={"service_center_group_employee_read"})
     * @ApiDoc(
     *  output="ClubEvo\Bundle\ApiBundle\Entity\ServiceCenterGroupEmployee"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('ServiceCenterGroupEmployee', $id);

        return $entity;
    }

    /**
     * Edits an existing Service Center Group Employee entity.
     *
     * @View()
     * @ApiDoc(
     *  input="ClubEvo\Bundle\ApiBundle\Form\Type\ServiceCenterGroupEmployeeType",
     *  output="ClubEvo\Bundle\ApiBundle\Entity\ServiceCenterGroupEmployee"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('ServiceCenterGroupEmployee', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new ServiceCenterGroupEmployeeType(),
            $entity,
            array(
                'action' => $this->generateUrl('service_center_group_employee_patch', array('id' => $entity->getId())),
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
                    'service_center_group_employee_read',
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
     * Creates a Service Center Group Employee entity.
     *
     * @View()
     * @ApiDoc(
     *  input="ClubEvo\Bundle\ApiBundle\Form\Type\ServiceCenterGroupEmployeeType",
     *  output="ClubEvo\Bundle\ApiBundle\Entity\ServiceCenterGroupEmployee"
     * )
     */
    public function addAction(Request $request)
    {
        $entity = new ServiceCenterGroupEmployee();

        /** @var Form $form */
        $form = $this->createForm(
            new ServiceCenterGroupEmployeeType(),
            $entity,
            array(
                'action' => $this->generateUrl('service_center_group_employee_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('service_center_group_employee_read', array('id' => $entity->getId())),
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
     * Destroys a Service Center Group Employee entity.
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('ServiceCenterGroupEmployee', $id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('service_center_group_employee_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('service_center_group_employee_browse', array()));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form
        );

    }
}
