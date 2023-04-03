<?php

namespace BillingTest\Values;

use Billing\Service\BillingFileService;
use Billing\Values\FileMessage;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 * @group billing_message
 */
class FileMessageTest extends TestCase
{
    private string $uuid;

    public function setUp(): void
    {
        parent::setUp();

        $this->uuid = '9c4021a8-5ab9-42c8-8395-7113907ad457';
    }

    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $postPayment = new FileMessage($this->uuid);

        $this->assertInstanceOf(FileMessage::class, $postPayment);
    }

    /**
     * @throws Exception
     */
    public function testUuidException()
    {
        $this->expectExceptionMessage(FileMessage::MESSAGE_EXCEPTION);

        $uuid = '';
        (new FileMessage($uuid));
    }

    /**
     * @throws Exception
     */
    public function testGetType()
    {
        $paymentMessage = new FileMessage($this->uuid);

        $typeClass = $paymentMessage->getType();
        $this->assertEquals(BillingFileService::class, $typeClass);
    }

    /**
     * @throws Exception
     */
    public function testGetUuid()
    {
        $paymentMessage = new FileMessage($this->uuid);

        $uuidClass = $paymentMessage->getUuid();
        $this->assertEquals($this->uuid, $uuidClass);
    }
}
