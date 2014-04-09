<?php

namespace Nsm\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseRegistrationController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * {@inhertidoc}
 */
class RegistrationController extends BaseRegistrationController
{

    /**
     * {@inhertidoc}
     */
    public function registerAction(Request $request)
    {
        return parent::registerAction($request);
    }

    /**
     * {@inhertidoc}
     */
    public function checkEmailAction()
    {
        return parent::checkEmailAction();
    }

    /**
     * {@inhertidoc}
     */
    public function confirmAction(Request $request, $token)
    {
        return parent::confirmAction($request, $token);
    }

    /**
     * {@inhertidoc}
     */
    public function confirmedAction()
    {
        return parent::confirmedAction();
    }
}
