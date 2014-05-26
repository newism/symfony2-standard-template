<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use Hateoas\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\ApiBundle\Entity\Invitation;
use Nsm\Bundle\ApiBundle\Entity\InvitationRepository;
use Nsm\Bundle\ApiBundle\Form\DataTransformer\InvitationToCodeTransformer;
use Nsm\Bundle\ApiBundle\Form\Type\InvitationClaimType;
use Nsm\Bundle\ApiBundle\Form\Type\InvitationFilterType;
use Nsm\Bundle\ApiBundle\Form\Type\InvitationType;
use Nsm\Bundle\CoreBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Invitation controller.
 */
class InvitationsController extends AbstractController
{
    protected $templateGroup = 'NsmApiBundle:Invitations';

    /**
     * @param $id
     *
     * @return mixed
     */
    private function findInvitationOr404($id)
    {
        $entity = $this->get('nsm_api.entity.invitation_repository')->find($id);

        if (!$entity instanceof Invitation) {
            throw new NotFoundHttpException('Invitation not found.');
        }

        return $entity;
    }

    /**
     * Browse all Invitation entities.
     *
     * @Get("/invitations.{_format}", name="invitation_browse", defaults={"_format"="~"})
     *
     * @View(templateVar="entities", serializerGroups={"invitation_browse"})
     * @QueryParam(name="page", requirements="\d+", default="1", strict=true, description="Page of the overview.")
     * @QueryParam(name="perPage", requirements="\d+", default="10", strict=true, description="Invitation count limit")
     * @ApiDoc(
     *  resource=true,
     *  filters={
     *      {"name"="page", "dataType"="integer"},
     *      {"name"="perPage", "dataType"="integer"},
     *      {"name"="orderBy", "dataType"="string", "pattern"="(id) ASC|DESC"}
     *  })
     */
    public function browseAction(Request $request, $page, $perPage)
    {
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
        $criteria = $invitationSearchForm->getData();

        $qb = $this->get('nsm_api.entity.invitation_repository')->createQueryBuilder();
        $qb->filterByCriteria($criteria);

        $pager = $this->paginateQuery($qb, $perPage, $page);

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
     * Finds and displays a invitation entity.
     *
     * @Get("/invitations/{id}.{_format}", name="invitation_read", requirements={"id" = "\d+"}, defaults={"_format"="~"})
     *
     * @View(templateVar="entity", serializerGroups={"invitation_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function readAction($id)
    {
        $entity = $this->findInvitationOr404($id);

        return $entity;
    }

    /**
     * Edits an existing invitation entity.
     *
     * @Patch("/invitations/{id}", name="invitation_patch")
     * @Get("/invitations/{id}/edit", name="invitation_edit")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\invitationType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->findInvitationOr404($id);

        /** @var Form $form */
        $form = $this->createForm(
            new InvitationType(new InvitationToCodeTransformer($em)),
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
     * Creates a add invitation entity.
     *
     * @Post("/invitations", name="invitation_post")
     * @Get("/invitations/add", name="invitation_add")
     *
     * @View()
     * @ApiDoc(
     *  input="Nsm\Bundle\ApiBundle\Form\Type\invitationType",
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Invitation();

        /** @var Form $form */
        $form = $this->createForm(
            new InvitationType(new InvitationToCodeTransformer($em)),
            $entity,
            array(
                'action' => $this->generateUrl('invitation_post'),
                'method' => 'POST'
            )
        )->add('Save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl('invitation_read', array('id' => $entity->getId())),
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
     * Destroys a invitation entity.
     *
     * @Delete("/invitations/{id}", name="invitation_delete")
     * @Get("/invitations/{id}/destroy", name="invitation_destroy")
     *
     * @View()
     * @ApiDoc()
     */
    public function destroyAction(Request $request, $id)
    {
        $entity = $this->findInvitationOr404($id);

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

    /**
     * Router for claiming an invitation
     *
     * @Get("/invitations/{code}/claim", name="invitation_claim")
     * @View()
     *
     * @param Request $request
     * @param         $code
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function claimAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var SecurityContext $securityContext */
        $securityContext = $this->get('security.context');

        /** @var InvitationRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Invitation');

        /** @var Invitation $invitation */
        $invitation = $repo->findOneByCode($code);

        // Invitation doesn't exist - Redirect to invitation 404
        if (null === $invitation) {
            $view = $this->view(
                array(
                    'code' => $code,
                )
            );

            $view->setStatusCode('404');
            $view->setTemplate($this->getTemplate('notFound'));

            return $this->handleView($view);
        }

        // User is not logged in - Redirect to register screen
        if (false === $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect(
                $this->generateUrl(
                    'fos_user_registration_register',
                    array(
                        'invitationCode' => $invitation->getCode(),
                        '_targetPath' => $request->getUri()
                    )
                )
            );
        }

        // User logged in - Send them to a confirm page
        if (true === $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect(
                $this->generateUrl(
                    'invitation_claim_confirm',
                    array(
                        'code' => $invitation->getCode(),
//                        '_targetPath' => $request->getUri()
                    )
                )
            );
        }

        // Catch all
        throw new \Exception('Could not determine Invitation claim action');
    }


    /**
     * Invitation Claim Confirmation
     *
     * @Get("/invitations/{code}/claim/confirm", name="invitation_claim_confirm")
     * @Post("/invitations/{code}/claim/confirm", name="post_invitation_claim_confirm")
     * @View()
     */
    public function claimConfirmAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var SecurityContext $securityContext */
        $securityContext = $this->get('security.context');

        /** @var InvitationRepository $repo */
        $repo = $em->getRepository('NsmApiBundle:Invitation');

        /** @var Invitation $invitation */
        $invitation = $repo->findOneByCode($code);

        $invitationClaimForm = $this->createForm(
            new InvitationClaimType(),
            array(
                'invitation' => $invitation
            ),
            array(
                'action' => $this->generateUrl(
                        'post_invitation_claim_confirm',
                        array(
                            'code' => $invitation->getCode()
                        )
                    ),
                'method' => 'POST'
            )
        )->add('Claim', 'submit');;

        $invitationClaimForm->handleRequest($request);

        if ($invitationClaimForm->isValid()) {

            // TODO: Claim invitation with manager

            $this->container->get('session')->getFlashBag()->add(
                'success',
                'Invitation Claimed via login'
            );

            return $this->redirect(
                $this->generateUrl(
                    'dashboard_browse'
                )
            );
        }

        return array(
            "invitationClaimForm" => $invitationClaimForm->createView()
        );

    }
}
