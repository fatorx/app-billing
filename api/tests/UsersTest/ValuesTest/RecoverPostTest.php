<?php

namespace UsersTest\ValuesTest;

use Exception;
use PHPUnit\Framework\TestCase;
use Users\Values\RecoverPost;

/**
 * @group user_values
 * @group recover
 */
class RecoverPostTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $postData = $this->getPostData();

        $userPost = new RecoverPost($postData);
        $this->assertInstanceOf(RecoverPost::class, $userPost);
    }

    /**
     * @throws Exception
     */
    public function testValidateData()
    {
        $postData = $this->getPostData();

        $userPost = new RecoverPost($postData);
        $this->assertInstanceOf(RecoverPost::class, $userPost);
        $status = $userPost->validate();

        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function testValidateDataEmailExcepion()
    {
        $this->expectExceptionMessage(RecoverPost::EXCEPTION_EMAIL_EMPTY);

        $postData = $this->getPostData();
        unset($postData['email']);

        $tokenPost = new RecoverPost($postData);
        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testValidateDataEmailInvalidExcepion()
    {
        $this->expectExceptionMessage(RecoverPost::EXCEPTION_EMAIL);

        $postData = $this->getPostData();
        $postData['email'] = 'email@dot';

        $tokenPost = new RecoverPost($postData);
        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testGetData()
    {
        $postData = $this->getPostData();

        $tokenPost = new RecoverPost($postData);
        $data = $tokenPost->getData();

        $this->assertTrue(isset($postData['email']));
        $this->assertTrue(isset($data['email']));
    }

    /**
     * @throws Exception
     */
    public function testGetRawData()
    {
        $postData = $this->getPostData();

        $tokenPost = new RecoverPost($postData);
        $data = $tokenPost->getRawData();

        $this->assertTrue(isset($postData['email']));
        $this->assertTrue(isset($data['email']));
    }

    public function getPostData(): array
    {
        return [
            'email' => 'luiz.gonzaga@mpb.com',
        ];
    }
}
