<?php

namespace BillingTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Entity\Invoice;
use Billing\Service\MailService;
use Billing\Values\MailMessage;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group billing
 * @group service
 */
class MailServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var MailService
     */
    protected MailService $service;


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  MailService $this->service */
        $this->service = $this->getApplicationServiceLocator()->get(MailService::class);
    }

    /**
     * @throws Exception
     */
    public function testProcess()
    {
        $mailMessage = $this->getMailMessage();
        $status = $this->service->process($mailMessage);

        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function testProcessException()
    {
        $this->expectExceptionMessage(MailService::MESSAGE_EXCEPTION_VALUE);

        $mailMessage = $this->getMailMessage();
        $mailMessage->getInvoice()
                    ->setAmount(0);
        $this->service->process($mailMessage);
    }

    /**
     * @throws Exception
     */
    public function getMailMessage(): MailMessage
    {
        return new MailMessage($this->getInvoice());
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
            'debt_id' => 6566,
        ];
    }
}
