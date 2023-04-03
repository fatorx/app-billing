<?php

namespace BillingTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Entity\Invoice;
use Billing\Service\BillingLineService;
use Billing\Values\LineMessage;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group billing
 * @group service
 */
class BillingLineServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var BillingLineService
     */
    protected BillingLineService $service;

    private int $debtId = 0;
    private array $data = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  BillingLineService $this->service */
        $this->service = $this->getApplicationServiceLocator()->get(BillingLineService::class);
        $this->service->setStdOut(false);

        $this->data = [
            'name' => '',
            'government_id' => '',
            'email' => '',
            'amount' => '',
            'due_date' => '',
            'debt_id' => $this->debtId,
            'status' => 0,
        ];
    }

    /**
     * @throws Exception
     */
    public function testProcess()
    {
        $this->configureScenario();

        $lineMessage = $this->getLineMessage();
        $status = $this->service->process($lineMessage);

        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function testProcessException()
    {
        $this->expectExceptionMessage(BillingLineService::MESSAGE_EXCEPTION_VALUE);

        $this->configureScenario(false);

        $lineMessage = $this->getLineMessage();
        $lineMessage->getInvoice()
            ->setAmount(0);

        $this->service->process($lineMessage);
    }

    /**
     * @throws Exception
     */
    public function testProcessIfExistDebitException()
    {
        $this->expectExceptionMessage(BillingLineService::MESSAGE_EXCEPTION_DEBT_CHECK);

        $this->configureScenario(true);

        $lineMessage = $this->getLineMessage();
        $this->service->process($lineMessage);
    }

    /**
     * @throws Exception
     */
    public function getLineMessage(): LineMessage
    {
        return new LineMessage($this->getInvoice());
    }

    /**
     * @throws Exception
     */
    public function getInvoice(): Invoice
    {
        return new Invoice($this->data);
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
    public function configureScenario($insertRegister = false)
    {
        $file = file('/tmp/test.csv');
        array_shift($file);

        $line = current($file);
        $parts = explode(',', trim($line));

        $this->debtId = $parts[5];
        $this->service->executeSql('DELETE FROM billings WHERE debt_id = ' . $this->debtId);

        $this->data = [
            'name' => $parts[0],
            'government_id' => $parts[1],
            'email' => $parts[2],
            'amount' => $parts[3],
            'due_date' => $parts[4],
            'debt_id' => $this->debtId,
            'status' => 0,
        ];

        if ($insertRegister) {
            $this->service->insert('billings', $this->data);
        }
    }
}
