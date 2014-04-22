<?php

namespace Nsm\Bundle\ApiBundle\Controller;

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
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Nsm\Bundle\ApiBundle\Entity\Activity;
use Nsm\Bundle\ApiBundle\Entity\ActivityManager;
use Nsm\Bundle\ApiBundle\Entity\ActivityRepository;
use Nsm\Bundle\ApiBundle\Entity\ActivityQueryBuilder;
use Nsm\Bundle\ApiBundle\Form\Type\ActivityType;
use Nsm\Bundle\ApiBundle\Form\Type\ActivityFilterType;

use Nsm\Bundle\FormBundle\Form\Model\DateRange;

/**
 * Activity controller.
 */
class ActivityController extends AbstractController
{
   /**
     * Browse all Activity entities.
     *
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Activity count limit")
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

        /** @var ActivityRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Activity');

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
        $criteria = $repo->sanatiseCriteria($activitySearchForm->getData());

        $qb = $repo->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);
        $results = $pager->getCurrentPageResults();
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
     * Finds and displays a Activity entity.
     *
     * @View(templateVar="entity", serializerGroups={"activity_read"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Activity"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('Activity', $id);

        return $entity;
    }

    /**
     * Edits an existing Activity entity.
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\ActivityType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Activity"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('Activity', $id);

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

        $responseData = array(
            'entity' => $entity,
            'form' => $form
        );

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Creates a Activity entity.
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\ActivityType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Activity"
     * )
     */
    public function addAction(Request $request)
    {
        $entity = new Activity();

        /** @var Form $form */
        $form = $this->createForm(
            new ActivityType(),
            $entity,
            array(
                'action' => $this->generateUrl('activity_add'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('activity_read', array('id' => $entity->getId())),
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
     * Destroys a Activity entity.
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('Activity', $id);

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
                return $this->redirect($this->generateUrl('activity_browse', array()));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form
        );

    }
}
