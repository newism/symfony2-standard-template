<?php

namespace Nsm\Bundle\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Nsm\Bundle\UserBundle\Form\Type\LoginType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * Add a form to the login data
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        $request = $this->getRequest();
        $invitationCode = $request->get('invitationCode');

        /** @var $loginForm Form */
        $loginForm = $this->createForm(
            new LoginType(),
            array(),
            array(
                'action' => $this->generateUrl(
                    'fos_user_security_check'
                ),
                'method' => 'POST'
            )
        )->add('Update', 'submit');

        $loginForm->submit(
            array(
                '_csrf_token' => $data['csrf_token'],
                '_username' => $data['last_username'],

                // Send the user to the invitation confirmation path if it exists
                // or whereever else they were going
                '_target_path' => $request->get('_targetPath'),

                // Send the user back to the same login page
                // with all of it's request params
                // including the ?invitationCode and target path params
                '_failure_path' => $request->getRequestUri(),

//                // This is not needed if we redirect to a confirmation page
//                // Add the invitation code
//                // This will be transformed into an invitation in the data transformer
                'invitation' => $invitationCode,
            )
        );

        $data['loginForm'] = $loginForm->createView();

        return parent::renderLogin($data);

    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * @param       $type
     * @param null  $data
     * @param array $options
     *
     * @return Form|\Symfony\Component\Form\FormInterface
     */
    public function createForm($type, $data = null, array $options = array())
    {
        return $this->container->get('form.factory')->create($type, $data, $options);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string         $route         The name of the route
     * @param mixed          $parameters    An array of parameters
     * @param Boolean|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

}
