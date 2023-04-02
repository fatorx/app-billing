<?php

namespace Billing\Message;

use Exception;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CallbackConsumer
{
    private ServiceManager $serviceManager;

    /**
     * @throws Exception
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke($message): void
    {
        try {

            $content = unserialize($message->body); // @todo check type
            $service = $this->serviceManager->get($content->getType());
            $service->process($content);

        } catch(Exception $e) {
            echo $e->getMessage();
            // @todo add log to options
        }

        $message->ack();
        // @todo remove message
    }
}
