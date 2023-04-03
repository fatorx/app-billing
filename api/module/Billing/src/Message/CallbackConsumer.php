<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Billing\Message;

use Application\Logs\Log;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CallbackConsumer
{
    use Log;

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
     * @throws Exception
     */
    public function __invoke($message): bool
    {
        try {
            $content = unserialize($message->body); // @todo check type
            $service = $this->serviceManager->get($content->getType());
            $service->process($content);

        } catch(Exception $e) {
            //throw $e;
            $this->addLog($e->getMessage());
            return false;
        }

        $message->ack();
        return true;
    }
}
