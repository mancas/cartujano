<?php
namespace Ecommerce\FrontendBundle\Component\Security;

use Ecommerce\CartBundle\Storage\CartStorageManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManager;

/**
 * Custom authentication success handler
 */
class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    /**
     * Constructor
     * @param RouterInterface   $router
     * @param EntityManager     $em
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from AbstractAuthenticationListener.
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response The response to return
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!in_array('ROLE_USER_NOT_VALIDATED', $user->getRoles())) {
            $cart = $request->getSession()->get(CartStorageManager::KEY);
            if (count($cart->getCartItems())) {
                $uri = $this->router->generate('cart_details');
            } else {
                $uri = $this->router->generate('user_profile');
            }
        } else {
            $uri = $this->router->generate('validate_user');
        }

        return new RedirectResponse($uri);
    }
}