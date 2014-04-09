<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Invitation controller.
 */
class InvitationsController extends AbstractController
{
    /**
     * Finds and displays a Invitation entity.
     *
     * @Get("/invitations/{id}/claim", name="invitations_claim", requirements={"id" = "\d+"})
     *
     * @View(templateVar="entity", serializerGroups={"invitation_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function claimAction($id)
    {
        $invitation = $entity = $this->find('Invitation', $id);

        // No invitation
        // Redirect to invitation 404
        if (null === $invitation) {
            $view = $this->view(array(
                'id' => $id,
            ));
            $view->setTemplate($this->getTemplate('claimNotFound'));
            return $view;
        }

        $securityContext = $this->get('security.context');
        // User is not logged in
        // Redirect to register screen
        if (false === $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect(
                $this->generateUrl(
                    'fos_user_security_login',
                    array(
                        'invitationId' => $invitation->getId()
                    )
                )
            );
        }

        return $this->redirect(
            $this->generateUrl(
                'invitations_claim_confirm',
                array(
                    'id' => $invitation->getId()
                )
            )
        );
    }


    /**
     * Finds and displays a Invitation entity.
     *
     * @Get("/invitations/{id}/claim/confirm", name="invitations_claim_confirm", requirements={"id" = "\d+"})
     *
     * @View(templateVar="entity", serializerGroups={"invitation_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function claimConfirmAction($id)
    {

    }


    /**
     * Finds and displays a Invitation entity.
     *
     * @Get("/invitations/{id}/claim/confirm/404", name="invitations_claim_not_found", requirements={"id" = "\d+"})
     *
     * @View(templateVar="entity", serializerGroups={"invitation_details"})
     * @ApiDoc(
     *  output="Nsm\Bundle\ApiBundle\Entity\Invitation"
     * )
     */
    public function claimConfirmNotFoundAction($id)
    {

    }
}
