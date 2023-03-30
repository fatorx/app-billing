<?php

namespace Users;

use Application\Service\Config as ConfigService;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Users\Controller\AccountController;
use Users\Controller\TokenController;
use Users\Controller\UsersController;
use Users\Listeners\AuthenticationListener;
use Users\Service\AccountService;
use Users\Service\AuthenticationService;
use Users\Service\TokenService;
use Users\Service\UserService;

class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();

        //(new AuthenticationListener())->attach($application->getEventManager(), 1); // @todo review token acess
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                UserService::class => function (ServiceManager $serviceManager) {
                    return (new ConfigService())->setup($serviceManager, new UserService());
                },
                TokenService::class => function (ServiceManager $serviceManager) {
                    return (new ConfigService())->setup($serviceManager, new TokenService());
                },
                AuthenticationService::class => function (ServiceManager $serviceManager) {
                    return (new ConfigService())->setup($serviceManager, new AuthenticationService());
                },
                AccountService::class => function (ServiceManager $serviceManager) {

                    $userService = $serviceManager->get(UserService::class);
                    $accountService = new AccountService($userService);

                    return (new ConfigService())->setup($serviceManager, $accountService);
                },
            ]
        ];
    }

    public function getControllerConfig(): array
    {
        return [
            'factories' => [
                TokenController::class => function (ServiceManager $serviceManager) {
                    /** @var  TokenService $tokenService */
                    $tokenService = $serviceManager->get(TokenService::class);

                    return new TokenController(
                        $tokenService
                    );
                },
                UsersController::class => function (ServiceManager $serviceManager) {
                    /** @var  UserService $userService */
                    $userService = $serviceManager->get(UserService::class);

                    return new UsersController(
                        $userService
                    );
                },
                AccountController::class => function (ServiceManager $serviceManager) {
                    /** @var  AccountService $tokenService */
                    $accountService = $serviceManager->get(AccountService::class);

                    return new AccountController(
                        $accountService
                    );
                }
            ]
        ];
    }

    /**
     * Expected to return an array of modules on which the current one depends on
     *
     * @return array
     */
    public function getModuleDependencies(): array
    {
        return [
            'Application'
        ];
    }
}
