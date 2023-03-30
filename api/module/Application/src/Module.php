<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnused */
/** @noinspection PhpUnused */
/** @noinspection PhpUnused */
/** @noinspection ALL */
/** @noinspection ALL */
/** @noinspection ALL */

/** @noinspection ALL */

namespace Application;

use Application\Service\RequestResponseService;
use Doctrine\ORM\EntityManager;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;

class Module
{
    const VERSION = '0.1';
    const CONFIG_KEY = 'config';

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     *
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e): void
    {
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                RequestResponseService::class => function (ServiceManager $serviceManager) {
                    $reqResService = new RequestResponseService();

                    /** @var EntityManager $entityManager */
                    $entityManager = $serviceManager->get(EntityManager::class);
                    $reqResService->setEm($entityManager);

                    $config = $serviceManager->get(self::CONFIG_KEY);
                    $reqResService->setConfig($config['ApiRequest']);

                    return $reqResService;
                }
            ]
        ];
    }
}
