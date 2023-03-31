<?php

namespace Billing;

use Application\Service\Config as ConfigService;
use Billing\Controller\BillingController;
use Billing\Message\Producer;
use Billing\Service\BillingService;
use Billing\Storage\StorageFile;
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
                    $storageFile = new StorageFile();
                    $producer = $serviceManager->get(Producer::class);
                    $billingService = new BillingService($storageFile, $producer);

                    return  (new ConfigService())->setup($serviceManager, $billingService);
                },
                Producer::class => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('config');
                    $configRabbit = $config['app']['rabbit_mq'];

                    return new Producer($configRabbit);
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