<?php

namespace UsersTest\ValuesTest;

use Exception;
use PHPUnit\Framework\TestCase;
use Users\Exception\UserException;
use Users\Values\UserPost;

/**
 * @group user_values
 */
class UserPostTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $postData = $this->getPostData();

        $userPost = new UserPost($postData);
        $this->assertInstanceOf(UserPost::class, $userPost);
    }

    /**
     * @throws Exception
     */
    public function testValidateData()
    {
        $postData = $this->getPostData();

        $userPost = new UserPost($postData);
        $this->assertInstanceOf(UserPost::class, $userPost);
        $status = $userPost->validate();

        $this->assertTrue($status);
    }

    /**
     * @throws Exception
     */
    public function testValidateDataNameExcepion()
    {
        $this->expectExceptionMessage(UserPost::EXCEPTION_NAME);

        $postData = $this->getPostData();
        unset($postData['name']);

        $tokenPost = new UserPost($postData);
        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testValidateDataNameLengthExcepion()
    {
        $this->expectExceptionMessage(UserPost::EXCEPTION_NAME_LENGTH);

        $postData = $this->getPostData();
        $postData['name'] = '123';

        $tokenPost = new UserPost($postData);
        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testValidateDataUserNameExcepion()
    {
        $this->expectExceptionMessage(UserPost::EXCEPTION_USERNAME);

        $postData = $this->getPostData();
        unset($postData['user_name']);

        $tokenPost = new UserPost($postData);
        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testValidateEmailLengthExcepion()
    {
        $this->expectExceptionMessage(UserPost::EXCEPTION_EMAIL_LENGTH);

        $postData = $this->getPostData();
        $postData['email'] = 'email@dot';

        $tokenPost = new UserPost($postData);
        $tokenPost->validate();
    }

    /**
     * @throws Exception
     */
    public function testGetData()
    {
        $postData = $this->getPostData();

        $tokenPost = new UserPost($postData);

        $data = $tokenPost->getData();

        $this->assertTrue(isset($postData['name']));
        $this->assertTrue(isset($postData['user_name']));
        $this->assertTrue(isset($postData['email']));

        $this->assertTrue(isset($data['name']));
        $this->assertTrue(isset($data['user_name']));
        $this->assertTrue(isset($data['email']));
    }

    /**
     * @throws Exception
     */
    public function testGetRawData()
    {
        $postData = $this->getPostData();

        $tokenPost = new UserPost($postData);

        $data = $tokenPost->getRawData();

        $this->assertTrue(isset($postData['name']));
        $this->assertTrue(isset($postData['user_name']));
        $this->assertTrue(isset($postData['email']));

        $this->assertTrue(isset($data['name']));
        $this->assertTrue(isset($data['user_name']));
        $this->assertTrue(isset($data['email']));
    }

    /**
     * @throws UserException
     */
    public function testGetDataExpose()
    {
        $postData = $this->getPostData();

        $tokenPost = new UserPost($postData);

        $data = $tokenPost->getDataExpose();

        $this->assertFalse(isset($data['password']));
    }

    public function getPostData(): array
    {
        return [
            'name' => 'Luiz Gonzaga do Nascimento',
            'user_name' => 'luiz-gonzaga',
            'email' => 'luiz.gonzaga@mpb.com',
        ];
    }
}
