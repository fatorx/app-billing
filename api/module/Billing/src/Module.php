<?php

namespace Billing;

use Application\Service\Config as ConfigService;
use Billing\Controller\BillingController;
use Billing\Service\BillingService;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ServiceManager\ServiceManager;
use Users\Service\UserService;

class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                BillingService::class => function (ServiceManager $serviceManager) {
                    return (new ConfigService())->setup($serviceManager, new BillingService());
                },
            ]
        ];
    }

    public function getControllerConfig(): array
    {
        return [
            'factories' => [
                BillingController::class => function (ServiceManager $serviceManager) {
                    /** @var  BillingService $billingService */
                    $billingService = $serviceManager->get(BillingService::class);

                    return new BillingController(
                        $billingService
                    );
                },
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
            'Application',
            'Users'
        ];
    }
}
