<?php

namespace BillingTest\Storage;

use Billing\Storage\StorageFile;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 * @group storage
 */
class StorageFileTest extends TestCase
{
    private string $pathStorage = '/mnt/api/data/storage';
    private string $pathFileTest = '/tmp/test.csv';
    private string $uuidFile = '9c4021a8-5ab9-42c8-8395-7113907ad457';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testUploadException()
    {
        $this->expectExceptionMessage(StorageFile::MESSAGE_FAIL_SEND_FILE);

        $storage = new StorageFile();

        $tmpName = '/tmp/file_tmp.csv';
        @$storage->upload($tmpName);
    }

    /**
     * @throws Exception
     */
    public function testZipFileException()
    {
        $this->expectExceptionMessage(StorageFile::MESSAGE_FAIL_SEND_FILE);

        $storage = new StorageFile('1234-1234-121231231');

        $tmpName = '/tmp/file_tmp.csv';
        @$storage->zipFile($tmpName);
    }

    /**
     * @throws Exception
     */
    public function testGet()
    {
        $fileName = $this->uuidFile . '.zip';
        $content = file_get_contents('/tmp/' . $fileName);
        file_put_contents($this->pathStorage . '/' . $fileName, $content);

        $storage = new StorageFile();
        $list = $storage->get($this->uuidFile);

        $this->assertIsArray($list);
        $this->assertNotNull($list);
    }

    /**
     * @throws Exception
     */
    public function testExtractException()
    {
        $this->expectExceptionMessage(StorageFile::MESSAGE_FAIL_GET_FILE);

        $storage = new StorageFile();
        $pathFile = '123.zip';
        $storage->extracted($pathFile);


    }


    public function tearDown(): void
    {
        parent::tearDown();

        @unlink($this->pathStorage . '/' . $this->uuidFile . '.zip');
        chmod($this->pathStorage, 777);
    }
}
