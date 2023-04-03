<?php

namespace BillingTest\Message;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Entity\Invoice;
use Billing\Message\CallbackConsumer;
use Billing\Values\MailMessage;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use LogicException;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 * @group callback
 */
class CallbackConsumerTest extends TestCase
{
    use ApplicationTestTrait;

    private CallbackConsumer $callbackConsumer;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplicationServiceLocator();
        $this->callbackConsumer = new CallbackConsumer($serviceManager);
    }

    /**
     * @throws LogicException
     */
    public function testInvoque()
    {
        $this->expectException(LogicException::class);

        $msg = $this->getMessageClass();

        $callback = $this->getClassTarget();
        $callback($msg);
    }

    /**
     * @throws Exception
     */
    public function getClassTarget(): CallbackConsumer
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->getApplicationServiceLocator();
        return new CallbackConsumer($serviceManager);
    }

    /**
     * @throws Exception
     */
    public function getMessageClass(): AMQPMessage
    {
        $invoice = $this->getInvoice();
        $message = new MailMessage($invoice);

        return new AMQPMessage($message->getMessage());
    }

    /**
     * @throws Exception
     */
    public function getInvoice(): Invoice
    {
        return new Invoice($this->getInvoiceData());
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
