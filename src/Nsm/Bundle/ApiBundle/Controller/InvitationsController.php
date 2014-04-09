<?php

namespace Nsm\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nsm\Bundle\ApiBundle\Form\Type\InvitationClaimType;

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

        }

        $securityContext = $this->get('security.context');
        // User is not logged in
        // Redirect to register screen
        if (false === $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

        }

        return $entity;
    }

}
