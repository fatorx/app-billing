<?php

namespace Billing;

use Application\Service\Config as ConfigService;
use Billing\Controller\BillingController;
use Billing\Message\ConnectionBroker;
use Billing\Message\Consumer;
use Billing\Message\Producer;
use Billing\Service\BillingFileService;
use Billing\Service\BillingLineService;
use Billing\Service\BillingService;
use Billing\Service\MailService;
use Billing\Service\PaymentService;
use Billing\Storage\StorageFile;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ServiceManager\ServiceManager;

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

                    /** @var Producer $producer */
                    $producer = $serviceManager->get(Producer::class);

                    /** @var Consumer $consumer */
                    $consumer = $serviceManager->get(Consumer::class);

                    $billingService = new BillingService($storageFile, $producer, $consumer);

                    return (new ConfigService())->setup($serviceManager, $billingService);
                },

                BillingFileService::class => function (ServiceManager $serviceManager) {
                    $storageFile = new StorageFile();

                    /** @var Producer $producer */
                    $producer = $serviceManager->get(Producer::class);

                    $billingService = new BillingFileService($storageFile, $producer);

                    return (new ConfigService())->setup($serviceManager, $billingService);
                },

                BillingLineService::class => function (ServiceManager $serviceManager) {
                    /** @var Producer $producer */
                    $producer = $serviceManager->get(Producer::class);
                    $billingLineService = new BillingLineService($producer);

                    return (new ConfigService())->setup($serviceManager, $billingLineService);
                },

                MailService::class => function (ServiceManager $serviceManager) {
                    $mailService = new MailService();
                    return (new ConfigService())->setup($serviceManager, $mailService);
                },

                PaymentService::class => function (ServiceManager $serviceManager) {
                    $paymentService = new PaymentService();
                    return (new ConfigService())->setup($serviceManager, $paymentService);
                },

                Producer::class => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get(ConfigService::CONFIG_KEY);
                    $configRabbit = $config['app']['rabbit_mq'];

                    $connectionBroker = ConnectionBroker::getInstance($configRabbit);
                    return new Producer($connectionBroker->getConnection());
                },

                Consumer::class => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get(ConfigService::CONFIG_KEY);
                    $configRabbit = $config['app']['rabbit_mq'];
                    $connectionBroker = ConnectionBroker::getInstance($configRabbit);

                    return new Consumer($serviceManager, $connectionBroker->getConnection());
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
