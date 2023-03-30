<?php /** @noinspection PhpUnused */

namespace Application\Controller;

use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Config
{
    /**
     * @param ServiceManager $serviceManager
     * @param IController $controller
     * @return IController
     */
    public function setup(ServiceManager $serviceManager, IController $controller): IController
    {
        try {
            $config = $serviceManager->get(Strings::CONFIG_KEY);
            $controller->setConfig($config['app']);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            echo $e->getMessage();
        }


        return $controller;
    }
}
