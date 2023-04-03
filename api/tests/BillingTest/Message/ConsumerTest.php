<?php

namespace BillingTest\Message;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Message\ChannelsConfig;
use Billing\Message\Consumer;
use Billing\Message\Producer;
use Billing\Values\FileMessage;
use Exception;
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
    }

    /**
     * @throws Exception
     */
    public function testWaitingMessages()
    {
        //$this->fail('Not implement method WaitingMessages');

        //$this->consumer->waitingMessages(ChannelsConfig::FILES);
        //$this->assertTrue($status);
    }
}
