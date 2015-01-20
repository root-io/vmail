<?php

namespace rootio\Bundle\vmailmeBundle\Authentication;

use Symfony\Component\Routing\RouterInterface,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    rootio\Bundle\vmailmeBundle\Lib\RoundcubeLogin;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $email = $token->getUser()->getEmail();
        $password = $request->request->get('_password');

        $rcl = new RoundcubeLogin($request, '/webmail/');

        try {
           $rcl->login($email, $password);
        } catch (RoundcubeLoginException $ex) {}

        return new RedirectResponse($this->router->generate('user_webmail'));
    }
}
