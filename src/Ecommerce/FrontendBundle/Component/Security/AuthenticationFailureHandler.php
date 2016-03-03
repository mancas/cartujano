<?php
namespace Ecommerce\FrontendBundle\Component\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Custom authentication success handler
 */
class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    private $router;

    /**
     * Constructor
     * @param RouterInterface   $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt fails. This
     * is called by authentication listeners inheriting from AbstractAuthenticationListener.
     * @param Request        $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->getFlashBag()->set('error', "El usuario o la contraseña introducidos no son correctos");
        return new RedirectResponse($this->router->generate('login'));
    }
}