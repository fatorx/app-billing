<?php

namespace BillingTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Entity\Payment;
use Billing\Service\PaymentService;
use Billing\Values\PaymentMessage;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group billing
 * @group service
 */
class PaymentServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var PaymentService
     */
    protected PaymentService $service;

    private int $debtId = 0;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  PaymentService $this->service */
        $this->service = $this->getApplicationServiceLocator()->get(PaymentService::class);
    }

    /**
     * @throws Exception
     */
    public function testProcess()
    {
        $this->configureScenario(true);

        $mailMessage = $this->getPaymentMessage();
        $status = $this->service->process($mailMessage);

        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function testProcessPaidAmountException()
    {
        $this->expectExceptionMessage(PaymentService::MESSAGE_EXCEPTION_VALUE);

        $paymentMessage = $this->getPaymentMessage();
        $paymentMessage->getPayment()
                       ->setPaidAmount(0);

        $this->service->process($paymentMessage);
    }

    /**
     * @depends testProcess
     * @throws Exception
     */
    public function testProcessPaymentAlreadyRegisterException()
    {
        $this->expectExceptionMessage(PaymentService::MESSAGE_EXCEPTION_PAYMENT_REGISTER);

        $this->configureScenario();

        $this->service->getEm()->persist($this->getPayment());
        $this->service->getEm()->flush();

        $paymentMessage = $this->getPaymentMessage();
        $this->service->process($paymentMessage);
    }

    /**
     * @depends testProcess
     * @throws Exception
     *
     */
    public function testProcessBillingNotRegisterException()
    {
        $this->expectExceptionMessage(PaymentService::MESSAGE_EXCEPTION_BILLING_NOT_REGISTER);

        $paymentMessage = $this->getPaymentMessage();
        $paymentMessage->getPayment()->setDebtId(123);

        $this->service->process($paymentMessage);
    }

    /**
     * @throws Exception
     */
    public function getPaymentMessage(): PaymentMessage
    {
        return new PaymentMessage($this->getPayment());
    }

    /**
     * @throws Exception
     */
    public function getPayment(): Payment
    {
        return new Payment($this->getInvoiceData());
    }

    /**
     * @return array
     */
    private function getInvoiceData(): array
    {
        return [
            'debtId' => $this->debtId,
            'paidAt' => new DateTime('2022-06-09 10:00:00'),
            'paidAmount' => 5125.31,
            'paidBy' => 'Charles Dickens',
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->service->executeSql('TRUNCATE billings');
        $this->service->executeSql('TRUNCATE payments');
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function configureScenario($deletePayment = false)
    {
        $file = file('/tmp/test.csv');
        array_shift($file);

        $line = current($file);
        $parts = explode(',', trim($line));

        $this->debtId = $parts[5];
        $this->service->executeSql('DELETE FROM billings WHERE debt_id = ' . $this->debtId);

        if ($deletePayment) {
            $this->service->executeSql('DELETE FROM payments WHERE debt_id = ' . $this->debtId);
        }

        $dataBilling = [
            'name' => $parts[0],
            'government_id' => $parts[1],
            'email' => $parts[2],
            'amount' => $parts[3],
            'due_date' => $parts[4],
            'debt_id' => $this->debtId,
            'status' => 0,
            'created_at' => $this->service->getDateTime(),
            'updated_at' => $this->service->getDateTime(),
        ];

        $this->service->insert('billings', $dataBilling);
    }
}
