<?php

namespace BillingTest\Message;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Message\Producer;
use Billing\Values\FileMessage;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group billing
 * @group producer
 */
class ProducerTest extends TestCase
{
    use ApplicationTestTrait;

    private Producer $producer;
    private string $uuid;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->uuid = '9c4021a8-5ab9-42c8-8395-7113907ad457';

        /** @var Producer $producer */
        $this->producer = $this->getApplicationServiceLocator()->get(Producer::class);
    }

    /**
     * @throws Exception
     */
    public function testCreateMessage()
    {
        $fileMessage = new FileMessage($this->uuid);
        $message = $fileMessage->getMessage();

        $status = $this->producer->createMessage($message);
        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function testCreateMessageException()
    {
        $this->expectExceptionMessage(Producer::EXCEPTION_MESSAGE_EMPTY);

        $message = '';
        $this->producer->createMessage($message);
    }
}
