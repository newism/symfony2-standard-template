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
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.registration.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->add('Register', 'submit');
        $form->setData($user);

        // CUSTOM CODE START

        /**
         * This checks if the invitation in the query string is valid.
         * It should always be valid as we can only get here via the /invitation/claim route at the moment
         * It is possible for people to just come to the registration page directly.
         *
         * If the method is safe (GET) look for an invitation code
         * then create a fake csrf token and bind the request
         * this validates the invitationCode and displays an error if required.
         *
         * Submitting the fake request means we don't have to load the invitaiton from the code manually
         * as it happens in the transformer
         */
        if ($request->isMethodSafe()) {
            $invitationCode = $request->get('invitationCode');
            // Get the form intention and create a token.
            // Todo: Would be nice to know how to disable the token
            $intention = $form->getConfig()->getOption('intention');
            $token = $this->container->get('form.csrf_provider')->generateCsrfToken($intention);
            $form->submit(
                array(
                    '_token' => $token,
                    'invitation' => $invitationCode,
                ),
                false
            );
        }

        // CUSTOM CODE END

        if ('POST' === $request->getMethod()) {
            $form->submit($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $userManager->updateUser($user);

                // Todo: Claim Invitation with manager

                $this->container->get('session')->getFlashBag()->add(
                    'success',
                    'Invitation Claimed via registration'
                );

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(
                    FOSUserEvents::REGISTRATION_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response)
                );

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse(
            'FOSUserBundle:Registration:register.html.' . $this->getEngine(),
            array(
                'form' => $form->createView(),
            )
        );
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
