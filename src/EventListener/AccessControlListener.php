<?php

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\RouterInterface;

class AccessControlListener
{
    private Security $security;
    private RouterInterface $router;
    private RequestStack $requestStack;

    private array $restrictedRoutes = [
        'app_ticket_fix',
    ];

    public function __construct(Security $security, RouterInterface $router, RequestStack $requestStack)
    {
        $this->security = $security;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route');

        if (in_array($currentRoute, $this->restrictedRoutes, true)) {
            $isAdmin = $this->security->isGranted('ROLE_ADMIN');
            $isStaff = $this->security->isGranted('ROLE_STAFF');

            // Se não for admin, redireciona para outra página
            if (!$isAdmin && !$isStaff) {
                $session = $this->requestStack->getSession();
                $session->getFlashBag()->add('error', 'Acesso negado! Não tens permissões para esta ação.');

                $event->setController(function () {
                    return new RedirectResponse($this->router->generate('app_ticket_all'));
                });
            }
        }
    }
}
