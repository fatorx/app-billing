<?php

namespace UsersTest\ValuesTest;

use Application\Util\Environment;
use Exception;
use PHPUnit\Framework\TestCase;
use Users\Values\TokenPost;

/**
 * @group user_values
 */
class TokenPostTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $postData = $this->getPostData();

        $appKey = '';
        $tokenPost = new TokenPost($postData, $appKey);

        $this->assertInstanceOf(TokenPost::class, $tokenPost);
    }

    /**
     * @throws Exception
     */
    public function testValidateData()
    {
        $postData = $this->getPostData();

        $appKey = '';
        $tokenPost = new TokenPost($postData, $appKey);
        $status = $tokenPost->validate();

        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function testValidateDataUsernameExcepion()
    {
        $this->expectExceptionMessage(TokenPost::EXCEPTION_USERNAME);

        $postData = $this->getPostData();
        unset($postData['username']);

        $appKey = '';
        $tokenPost = new TokenPost($postData, $appKey);

        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testValidateDataPasswordExcepion()
    {
        $this->expectExceptionMessage(TokenPost::EXCEPTION_PASSWORD);

        $postData = $this->getPostData();
        unset($postData['password']);

        $appKey = '';
        $tokenPost = new TokenPost($postData, $appKey);

        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testValidateDataPasswordLengthExcepion()
    {
        $this->expectExceptionMessage(TokenPost::EXCEPTION_PASSWORD_LENGTH);

        $postData = $this->getPostData();
        $postData['password'] = '1234';

        $appKey = '';
        $tokenPost = new TokenPost($postData, $appKey);

        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testGetData()
    {
        $postData = $this->getPostData();

        $appKey = '';
        $tokenPost = new TokenPost($postData, $appKey);

        $data = $tokenPost->getData();

        $this->assertTrue(isset($postData['username']));
        $this->assertTrue(isset($postData['password']));

        $this->assertTrue(isset($data['username']));
        $this->assertTrue(isset($data['password']));
    }

    /**
     * @throws Exception
     */
    public function testGetRawData()
    {
        $postData = $this->getPostData();

        $appKey = '';
        $tokenPost = new TokenPost($postData, $appKey);

        $data = $tokenPost->getRawData();

        $this->assertTrue(isset($postData['username']));
        $this->assertTrue(isset($postData['password']));

        $this->assertTrue(isset($data['username']));
        $this->assertTrue(isset($data['password']));
    }

    public function getPostData(): array
    {
        return [
            'username' => Environment::env('APP_USERNAME'),
            'password' => Environment::env('APP_PASSWORD'),
        ];
    }
}
