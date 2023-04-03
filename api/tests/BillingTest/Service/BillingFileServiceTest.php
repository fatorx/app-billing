<?php

namespace BillingTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Entity\Invoice;
use Billing\Service\BillingFileService;
use Billing\Service\BillingLineService;
use Billing\Values\FileMessage;
use Billing\Values\LineMessage;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group billing
 * @group service
 */
class BillingFileServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var BillingFileService
     */
    protected BillingFileService $service;

    private string $uuid;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->uuid = '9c4021a8-5ab9-42c8-8395-7113907ad457';

        $fileZip = "/tmp/{$this->uuid}.zip";
        $pathApp = "/mnt/api/data/storage/{$this->uuid}.zip";
        copy($fileZip, $pathApp);

        /** @var  BillingFileService $this->service */
        $this->service = $this->getApplicationServiceLocator()->get(BillingFileService::class);
    }

    /**
     * @throws Exception
     */
    public function testProcess()
    {
        $lineMessage = $this->getFileMessage();
        $status = $this->service->process($lineMessage);

        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function getFileMessage(): FileMessage
    {
        return new FileMessage($this->uuid);
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
}
