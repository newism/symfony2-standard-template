<?php

namespace Nsm\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * {@inhertidoc}
 */
class SecurityController extends BaseSecurityController
{
    /**
     * @{inheritDoc}
     */
    public function loginAction(Request $request)
    {
        return parent::loginAction($request);
    }

    /**
     * @{inheritDoc}
     */
    protected function renderLogin(array $data)
    {
        return parent::renderLogin($data);
    }
}
