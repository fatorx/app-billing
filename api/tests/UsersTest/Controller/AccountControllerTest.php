<?php

namespace UsersTest\Controller;

use ApplicationTest\Controller\BaseControllerTest;
use Exception;
use Users\Controller\AccountController;

/**
 * @group controllers
 * @group users
 * @group account
 */
class AccountControllerTest extends BaseControllerTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->module = 'users';
        $this->controllerName = AccountController::class;
        $this->controllerClass = 'AccountController';
    }

    /**
     * @group account_resume
     * @throws Exception
     */
    public function testAccountResume()
    {
        $this->getRequestHeadersJwt('');

        $this->dispatch('/user-account', 'GET');

        $this->moduleTest(200, 'user-account');
    }

    /**
     * @group account_resume
     * @throws Exception
     */
    public function testAccountResumeTokenInvalid()
    {
        $this->getRequestHeadersJwt('', true);

        $this->dispatch('/user-account', 'GET');

        $this->moduleTest(400, 'user-account');
    }

    /**
     * @throws Exception
     */
    public function testAccountResumeData()
    {
        $this->getRequestHeadersJwt();

        $this->dispatch('/user-account', 'GET');
        $data = $this->getResponseContent();

        $this->assertTrue(isset($data['result']['user']), 'User data not set!');

        $userData = $data['result']['user'];
        $this->assertTrue(isset($userData['id']), 'User ID not set!');
    }

    /**
     * @group recover
     *
     * @throws Exception
     */
    public function testRecoverPassword()
    {
        $postData = [
            'email' => 'email@gmai.com'
        ];
        $this->configurePostJson($postData);

        $this->dispatch('/user-account/recover-password', 'POST');

        $this->moduleTest(200, 'user-account/recover-password');
    }

    /**
     * @group recover
     *
     * @throws Exception
     */
    public function testRecoverPasswordWithInvalidEmail()
    {
        $postData = [
            'email' => ''
        ];

        $this->configurePostJson($postData);

        $this->dispatch('/user-account/recover-password', 'POST');

        $this->moduleTest(400, 'user-account/recover-password');
    }

    /**
     * @group logout
     *
     * @throws Exception
     */
    public function testLogoutAccount()
    {
        $this->getRequestHeadersJwt();

        $this->dispatch('/user-account/logout', 'GET');

        $this->moduleTest(200, 'user-account/logout');

    }
}
