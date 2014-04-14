<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\ApiBundle\Entity\Invitation;
use Nsm\Bundle\ApiBundle\Form\Type\InvitationClaimType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Invitation controller.
 */
class InvitationsController extends AbstractController
{
    /**
     * Router for claiming an invitation
     *
     * @Get("/invitations/{code}/claim", name="invitations_claim")
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
        /** @var SecurityContext $securityContext */
        $securityContext = $this->get('security.context');

        /** @var Invitation $invitation */
        $invitation = $this->getEntityManager()->getRepository()->findOneByCode($code);

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
                    'invitations_claim_confirm',
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
     * @Get("/invitations/{code}/claim/confirm", name="invitations_claim_confirm")
     * @Post("/invitations/{code}/claim/confirm", name="post_invitations_claim_confirm")
     * @View()
     */
    public function claimConfirmAction(Request $request, $code)
    {
        /** @var Invitation $invitation */
        $invitation = $this->getEntityManager()->getRepository()->findOneByCode($code);

        $invitationClaimForm = $this->createForm(
            new InvitationClaimType(),
            array(
                'invitation' => $invitation
            ),
            array(
                'action' => $this->generateUrl(
                        'post_invitations_claim_confirm',
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
