<?php

namespace Mlf\UserBundle\Component\Authentication\Handler;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Nsm\Bundle\AppBundle\Entity\InvitationManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Authentication handler to manage invites being claimed via logging in
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var \Nsm\Bundle\AppBundle\Entity\InvitationManager $invitationManager
     */
    private $invitationManager;
    /**
     * @var \Symfony\Component\Routing\RouterInterface $router
     */
    private $router;
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session $session
     */
    private $session;
    /**
     * @var \Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    private $securityContext;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    private $doctrine;

    /**
     * @param Registry          $doctrine
     * @param HttpUtils         $httpUtils
     * @param array             $options
     * @param RouterInterface   $router
     * @param Session           $session
     * @param SecurityContext   $securityContext
     * @param InvitationManager $invitationManager
     */
    public function __construct(
        Registry $doctrine,
        HttpUtils $httpUtils,
        $options,
        RouterInterface $router,
        Session $session,
        SecurityContext $securityContext,
        InvitationManager $invitationManager
    )
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
        $this->session = $session;
        $this->securityContext = $securityContext;
        $this->invitationManager = $invitationManager;

        $options = array_merge(
            array(
                'always_use_default_target_path' => true,
                'default_target_path' => '/'
            ),
            $options
        );

        parent::__construct($httpUtils, $options);

    }

    /**
     * Authentication success
     *
     * Validate and process the invite after user logs in.
     * If the invite cannot be found log the user out and redirect back to the login screen
     * Todo: The invite should be validated before the user logs in, not after… I think this is impossible
     *
     * @param \Symfony\Component\HttpFoundation\Request                            $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();
        $invitationCode = $request->request->get('invitationCode');

        if (false != $invitationCode) {

            $invitationRepo = $this->doctrine->getRepository('MlfAppBundle:Invitation');
            $invitation = $invitationRepo->findOneBy(
                array(
                    'id' => $invitationCode
                )
            );

            if (null === $invitation) {

                // Log the user out
                $this->securityContext->setToken(null);
                $request->getSession()->invalidate();

                $redirectUrl = $this->router->generate(
                    'fos_user_security_login',
                    array(
                        'invitationCode' => $invitationCode,
                        'last_username' => $request->request->get('_username')
                    )
                );

                return $this->httpUtils->createRedirectResponse($request, $redirectUrl);
            }

            // Process the invitation
            if (null !== $invitation) {
                $this->invitationManager->claimInvitation($invitation, $user);
            }
        }

        // redirect when finished
        return parent::onAuthenticationSuccess($request, $token);
    }

}
