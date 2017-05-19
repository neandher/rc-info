<?php

namespace AdminBundle\Controller;

use AppBundle\Event\FlashBagEvents;
use UserBundle\Security\UserImpersonator;
use AppBundle\Util\FlashBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ImpersonateUserController
{
    /**
     * @var UserImpersonator
     */
    protected $impersonator;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $authorizationRole;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param UserImpersonator $impersonator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserProviderInterface $userProvider
     * @param RouterInterface $router
     * @param string $authorizationRole
     * @param FlashBag $flashBag
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UserImpersonator $impersonator,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProviderInterface $userProvider,
        RouterInterface $router,
        $authorizationRole,
        FlashBag $flashBag,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->impersonator = $impersonator;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider = $userProvider;
        $this->router = $router;
        $this->authorizationRole = $authorizationRole;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request $request
     * @param string $username
     *
     * @return Response
     */
    public function impersonateAction(Request $request, $username)
    {
        if (!$this->authorizationChecker->isGranted($this->authorizationRole)) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userProvider->loadUserByUsername($username);
        if (null === $user) {
            throw new HttpException(Response::HTTP_NOT_FOUND);
        }

        $this->impersonator->impersonate($user);

        $this->addFlash($request, $username);

        if ($request->headers->has('referer')) {
            return new RedirectResponse($request->headers->get('referer'));
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_dashboard'));
    }

    /**
     * @param Request $request
     * @param string $username
     */
    private function addFlash(Request $request, $username)
    {
        $this->flashBag->newMessage(
            FlashBagEvents::MESSAGE_TYPE_SUCCESS,
            'security.customer.impersonate',
            ['%name%' => $username],
            $request->getSession()
        );
    }
}