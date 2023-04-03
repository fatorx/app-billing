<?php

namespace Application\Service;

use Laminas\ServiceManager\ServiceManager;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Redis;
use RedisException;

class Config
{
    const CONFIG_KEY = 'config';

    /**
     * @param ServiceManager $serviceManager
     * @param IService $service
     *
     * @return IService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     */
    public function setup(ServiceManager $serviceManager, IService $service): IService|null
    {
        $config = $serviceManager->get(self::CONFIG_KEY);
        $service->setConfig($config['app']);

        //$req = $serviceManager->get('router');

        try {
            /** @var EntityManager $entityManager */
            $entityManager = $serviceManager->get(EntityManager::class);
            $service->setEm($entityManager);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            //echo $e->getMessage();

            return null;
        }

//        $redis = new Redis();
//        $redis->connect($config['app']['redis_host']);
//        $service->setStorage($redis);

        return $service;
    }
}
