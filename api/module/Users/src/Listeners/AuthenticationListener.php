<?php

namespace Users\Listeners;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Stdlib\ResponseInterface;
use Laminas\View\Model\JsonModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Users\Service\AuthenticationService;

class AuthenticationListener extends AbstractListenerAggregate
{
    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH,
            [$this, 'userHasAuthentication']
        );
    }

    /**
     * @param MvcEvent $event
     * @return Response|ResponseInterface|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function userHasAuthentication(MvcEvent $event): Response|ResponseInterface|null
    {
        /**@var AbstractActionController $target */
        $target = $event->getTarget();
        $isAuthorizationRequired = $event->getRouteMatch()->getParam('isAuthorizationRequired');
        $response = $target->getResponse();

        if ($isAuthorizationRequired) {

            /** @var  AuthenticationService $authenticationService */
            $authenticationService = $event->getApplication()->getServiceManager()->get(AuthenticationService::class);

            if ($authenticationService->hasIdentity() === false) {
                $event->stopPropagation();

                $response->setStatusCode(401);
                $response->sendHeaders();

                // @todo add redis service to check the token

                $data = [
                    'token' => 'item',
                    'user' => 'fabiosmendes',
                    'is_authorization' => $isAuthorizationRequired
                ];
                $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
                $view = new JsonModel($data);
                $response->setContent($view->serialize());
            }
        }

        return $response;
    }
}
