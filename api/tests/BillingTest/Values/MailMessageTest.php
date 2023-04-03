<?php

namespace BillingTest\Values;

use Billing\Entity\Invoice;
use Billing\Service\MailService;
use Billing\Values\MailMessage;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 * @group billing_message
 */
class MailMessageTest extends TestCase
{
    private array $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = $this->getInvoiceData();
    }

    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $invoice = $this->getInvoice();
        $lineMessage = new MailMessage($invoice);

        $this->assertInstanceOf(MailMessage::class, $lineMessage);
    }

    /**
     * @throws Exception
     */
    public function testEmailException()
    {
        $this->expectExceptionMessage(MailMessage::MESSAGE_EXCEPTION);

        $invoice = $this->getInvoice();
        $invoice->setEmail('');

        (new MailMessage($invoice));
    }

    /**
     * @throws Exception
     */
    public function testGetType()
    {
        $invoice = $this->getInvoice();
        $lineMessage = new MailMessage($invoice);

        $typeClass = $lineMessage->getType();
        $this->assertEquals(MailService::class, $typeClass);
    }

    /**
     * @throws Exception
     */
    public function testGetInvoice()
    {
        $invoice = $this->getInvoice();
        $lineMessage = new MailMessage($invoice);

        $invoiceInternal = $lineMessage->getInvoice();
        $this->assertEquals($invoiceInternal, $invoice);
    }

    /**
     * @throws Exception
     */
    public function testGetMessage()
    {
        $invoice = $this->getInvoice();
        $lineMessage = new MailMessage($invoice);

        $serializeObj = serialize($lineMessage);

        $serializeInternal = $lineMessage->getMessage();
        $this->assertEquals($serializeInternal, $serializeObj);
    }

    /**
     * @throws Exception
     */
    public function getInvoice(): Invoice
    {
        return new Invoice($this->data);
    }

    /**
     * @return array
     */
    private function getInvoiceData(): array
    {
        return [
            'name' => 'Charles Dickens',
            'government_id' => '11111111111',
            'email' => 'charlesdickens@kanastra.com.br',
            'amount' => '10.00',
            'due_date' => '2023-03-30',
            'debt_id' => (int)6566,
        ];
    }
}
