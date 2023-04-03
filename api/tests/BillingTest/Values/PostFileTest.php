<?php

namespace BillingTest\Values;

use Billing\Values\PostFile;
use Exception;
use Laminas\Stdlib\Parameters;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 */
class PostFileTest extends TestCase
{
    private string $pathFile;

    public function setUp(): void
    {
        parent::setUp();

        $this->pathFile = '/tmp/test.csv';
    }

    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $length = $this->getLength($this->pathFile);

        $postData = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/csv',
                'tmp_name' => $this->pathFile,
                'error' => 0,
                'size' => $length
            ]
        ]);

        $userPost = new PostFile($postData);
        $this->assertInstanceOf(PostFile::class, $userPost);
        $this->assertEquals($postData, $userPost->getData());
    }

    /**
     * @throws Exception
     */
    public function testGetType()
    {
        $length = $this->getLength($this->pathFile);

        $type = 'text/csv';

        $postData = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => $type,
                'tmp_name' => $this->pathFile,
                'error' => 0,
                'size' => $length
            ]
        ]);

        $userPost = new PostFile($postData);
        $this->assertEquals($type, $userPost->getType());
    }

    /**
     * @throws Exception
     */
    public function testGetTmpName()
    {
        $length = $this->getLength($this->pathFile);

        $type = 'text/csv';

        $postData = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => $type,
                'tmp_name' => $this->pathFile,
                'error' => 0,
                'size' => $length
            ]
        ]);

        $userPost = new PostFile($postData);
        $this->assertEquals($this->pathFile, $userPost->getTmpName());
    }

    /**
     * @throws Exception
     */
    public function testValidFileException()
    {
        $this->expectExceptionMessage(PostFile::MESSAGE_INVALID_FILE);

        $postData = new Parameters([]);
        (new PostFile($postData));
    }

    /**
     * @throws Exception
     */
    public function testValidTmpFileException()
    {
        $this->expectExceptionMessage(PostFile::MESSAGE_INVALID_FILE);

        $postData = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/csv',
                'tmp_name' => '',
                'error' => 0,
                'size' => 0
            ]
        ]);

        (new PostFile($postData));
    }

    /**
     * @throws Exception
     */
    public function testValidFormatException()
    {
        $this->expectExceptionMessage(PostFile::MESSAGE_INVALID_FORMAT);

        $length = $this->getLength($this->pathFile);

        $postData = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/plain',
                'tmp_name' => $this->pathFile,
                'error' => 0,
                'size' => $length
            ]
        ]);

        (new PostFile($postData));
    }

    /**
     * @throws Exception
     */
    public function testValidSizeException()
    {
        $this->expectExceptionCode(PostFile::EXCEPTION_CODE_SIZE);

        $filePath = '/tmp/test.csv';
        $length = 2000000;

        $postData = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/csv',
                'tmp_name' => $filePath,
                'error' => 0,
                'size' => $length
            ]
        ]);

        (new PostFile($postData));
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
}
