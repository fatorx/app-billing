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
    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $filePath = '/tmp/test.csv';
        $length = $this->getLength($filePath);

        $postData = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/csv',
                'tmp_name' => $filePath,
                'error' => 0,
                'size' => $length
            ]
        ]);

        $userPost = new PostFile($postData);
        $this->assertInstanceOf(PostFile::class, $userPost);
        $this->assertEquals($postData, $userPost->getData());
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
