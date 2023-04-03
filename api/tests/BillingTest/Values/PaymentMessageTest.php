<?php

namespace BillingTest\Values;

use Billing\Entity\Payment;
use Billing\Service\PaymentService;
use Billing\Values\PaymentMessage;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 * @group billing_message
 */
class PaymentMessageTest extends TestCase
{
    private array $data;

    public function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'debtId' => 123,
            'paidAt' => new DateTime('2023-02-12 10:20:00'),
            'paidAmount' => 1001.10,
            'paidBy' => 'John Doe'
        ];
    }

    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $payment = $this->getPaymentEntity();
        $postPayment = new PaymentMessage($payment);
        $paymentInternal = $postPayment->getPayment();

        $this->assertInstanceOf(PaymentMessage::class, $postPayment);
        $this->assertEquals($payment, $paymentInternal);
    }

    /**
     * @throws Exception
     */
    public function testPaidAmountException()
    {
        $this->expectExceptionMessage(PaymentMessage::MESSAGE_EXCEPTION);

        $payment = $this->getPaymentEntity();
        $payment->setPaidAmount(0);

        (new PaymentMessage($payment));
    }

    /**
     * @throws Exception
     */
    public function testGetType()
    {
        $payment = $this->getPaymentEntity();
        $paymentMessage = new PaymentMessage($payment);

        $typeClass = $paymentMessage->getType();
        $this->assertEquals(PaymentService::class, $typeClass);
    }

    /**
     * @throws Exception
     */
    public function getPaymentEntity(): Payment
    {
        return new Payment($this->data);
    }
}
