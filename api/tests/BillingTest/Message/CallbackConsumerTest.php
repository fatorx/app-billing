<?php

namespace BillingTest\Message;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Message\CallbackConsumer;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CallbackConsumerTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @throws Exception
     */
    public function testInvoque()
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplicationServiceLocator();
        $callbackConsumer = new CallbackConsumer($serviceManager);

        $serializeContent = '';

        try {
            $callbackConsumer();
        } catch (NotFoundExceptionInterface $e) {
        } catch (ContainerExceptionInterface $e) {
        }
    }
}
