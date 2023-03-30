<?php

namespace ApplicationTest\Util;

use Dotenv\Dotenv;
use Laminas\Mvc\Application;
use Laminas\Mvc\ApplicationInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\Stdlib\ArrayUtils;

trait ApplicationTestTrait
{
    protected null|ApplicationInterface $application = null;

    public function getApplication(): ApplicationInterface
    {
        if ($this->application) {
            return $this->application;
        }

        $path = __DIR__ . '/../../../';

        $dotenv = Dotenv::createImmutable($path, '.env');
        $dotenv->load();

        $configOverrides = include $path . 'config/autoload/local.php';

        $this->application = Application::init(ArrayUtils::merge(
            include $path . 'config/application.config.php',
            $configOverrides
        ));

        return $this->application;
    }

    public function getApplicationServiceLocator(): ServiceLocatorInterface
    {
        return $this->getApplication()->getServiceManager();
    }

}
