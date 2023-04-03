<?php

namespace BillingTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Service\BillingService;
use Billing\Storage\StorageFile;
use Billing\Values\PostFile;
use Billing\Values\PostPayment;
use Exception;
use Laminas\Stdlib\Parameters;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group billing
 * @group service
 */
class BillingServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var BillingService
     */
    protected BillingService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  BillingService $this- >service */
        $this->service = $this->getApplicationServiceLocator()->get(BillingService::class);
    }

    /**
     * @throws Exception
     */
    public function testStorageFile()
    {
        $filePath = '/tmp/test.csv';
        $length = $this->getLength($filePath);

        $upload = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/csv',
                'tmp_name' => $filePath,
                'error' => 0,
                'size' => $length
            ]
        ]);

        $postFile = new PostFile($upload);

        $uuid = $this->service->storage($postFile);
        $this->assertIsString($uuid);

        $limitLengthName = 36; // uuid length
        $isEquals = (strlen($uuid) === $limitLengthName);
        $this->assertTrue($isEquals);
    }

    /**
     * @throws Exception
     */
    public function testStorageFileNull()
    {
        $this->configHhandlerError();

        $this->expectExceptionMessage('Não foi possível armazenar o arquivo.');

        $filePath = '/tmp/test1.csv';
        $length = $this->getLength($filePath);

        $upload = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/csv',
                'tmp_name' => $filePath,
                'error' => 0,
                'size' => $length
            ]
        ]);

        $postFile = new PostFile($upload);

        $uuid = @$this->service->storage($postFile);
        $this->assertEmpty($uuid);

        restore_error_handler();
    }

    /**
     * @param string $file
     * @return int
     */
    private function getLength(string $file): int
    {
        $content = file_get_contents($file);
        return strlen($content);
    }

    /**
     * @throws Exception
     */
    public function testConfirmPayment()
    {
        $sendData = [
            'debtId' => '123',
            'paidAt' => '2022-06-09 10:00:00',
            'paidAmount' => 101.25,
            'paidBy' => 'John Doe'
        ];
        $postPayment = new PostPayment($sendData);
        $isTrue = $this->service->confirmPayment($postPayment);

        $this->assertTrue($isTrue);
    }

    /**
     * @return void
     * @throws Exception
     */
    private function configHhandlerError(): void
    {
        set_error_handler(
            /**
             * @throws Exception
             */
            static function (int $errno, string $errstr): never {
                throw new Exception($errstr, $errno);
            },
            E_USER_WARNING
        );
    }
}
