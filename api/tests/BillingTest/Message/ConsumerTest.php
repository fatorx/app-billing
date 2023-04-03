<?php

namespace BillingTest\Message;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Message\ChannelsConfig;
use Billing\Message\Consumer;
use Exception;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group billing
 * @group consumer
 */
class ConsumerTest extends TestCase
{
    use ApplicationTestTrait;

    private Consumer $consumer;
    private string $uuid;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var Consumer $consumer */
        $this->consumer = $this->getApplicationServiceLocator()->get(Consumer::class);
        $this->consumer->setStdOut(false);
    }

    /**
     * @throws Exception
     */
    public function testWaitingMessages()
    {
        $this->consumer->waitingMessages();
        $statusService = $this->consumer->getStartService();

        $this->assertTrue($statusService);
    }

    /**
     * @throws AMQPTimeoutException
     */
    public function testWaitMessagesException()
    {
        $this->expectExceptionMessage('The connection timed out after 1 sec while awaiting incoming data');

        $this->consumer->waitingMessages(ChannelsConfig::FILES, 1);
    }
}
