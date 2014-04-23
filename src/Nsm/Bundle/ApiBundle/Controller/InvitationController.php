<?php

namespace Nsm\Bundle\ApiBundle\Controller;

// Core
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

// Third Party
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

// Project Based
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Nsm\Bundle\ApiBundle\Entity\Invitation;
use Nsm\Bundle\ApiBundle\Entity\InvitationManager;
use Nsm\Bundle\ApiBundle\Entity\InvitationRepository;
use Nsm\Bundle\ApiBundle\Entity\InvitationQueryBuilder;
use Nsm\Bundle\ApiBundle\Form\Type\InvitationType;
use Nsm\Bundle\ApiBundle\Form\Type\InvitationFilterType;

use Nsm\Bundle\FormBundle\Form\Model\DateRange;

/**
 * Invitation controller.
 */
class InvitationController extends AbstractController
{
   /**
     * Browse all Invitation entities.
     *
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Invitation count limit")
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

        /** @var InvitationRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Invitation');

        /** @var Form $form */
        $invitationSearchForm = $this->createForm(
            new InvitationFilterType(),
            array(),
            array(
                'action' => $this->generateUrl('invitation_browse'),
                'method' => 'GET'
            )
        )->add('search', 'submit');

        $invitationSearchForm->handleRequest($request);
        $criteria = $repo->sanatiseCriteria($invitationSearchForm->getData());

        $qb = $repo->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);
        $results = $pager->getCurrentPageResults();
        $responseData = array();

        if (true === $this->getViewHandler()->isFormatTemplating($request->getRequestFormat())) {
            $responseData['pager'] = $pager;
            $responseData['search_form'] = $invitationSearchForm->createView();
        } else {

            $paginatedCollection = $this->createPaginatedCollection(
                $pager,
                new Route('invitation_browse', array())
            );

            $responseData = $paginatedCollection;
        }

        $view = $this->view($responseData);
        $view->setTemplate($this->getTemplate($request->query->get('_template', 'browse')));

        return $view;
    }

    /**
     * Finds and displays a Invitation entity.
     *
     * @View(templateVar="entity", serializerGroups={"invitation_read"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findOr404('Invitation', $id);

        return $entity;
    }

    /**
     * Edits an existing Invitation entity.
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\InvitationType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findOr404('Invitation', $id);

        /** @var Form $form */
        $form = $this->createForm(
            new InvitationType(),
            $entity,
            array(
                'action' => $this->generateUrl('invitation_patch', array('id' => $entity->getId())),
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
                    'invitation_read',
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
     * Creates a Invitation entity.
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\InvitationType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function addAction(Request $request)
    {
        $entity = new Invitation();

        /** @var Form $form */
        $form = $this->createForm(
            new InvitationType(),
            $entity,
            array(
                'action' => $this->generateUrl('invitation_add'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('invitation_read', array('id' => $entity->getId())),
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
     * Destroys a Invitation entity.
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findOr404('Invitation', $id);

        /** @var Form $form */
        $form = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->setAction($this->generateUrl('invitation_destroy', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            if ($this->get('fos_rest.view_handler')->isFormatTemplating($request->getRequestFormat())) {
                return $this->redirect($this->generateUrl('invitation_browse', array()), Codes::HTTP_OK);
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
