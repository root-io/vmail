<?php

namespace rootio\Bundle\vmailmeBundle\Authentication;

use Symfony\Component\Routing\RouterInterface,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface,
    rootio\Bundle\vmailmeBundle\Lib\RoundcubeLogin;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onLogoutSuccess(Request $request)
    {
        $rcl = new RoundcubeLogin($request, '/webmail/');

        try {
           $rcl->logout();

        } catch (RoundcubeLoginException $ex) {}

        return new RedirectResponse($this->router->generate('homepage'));
    }
}
