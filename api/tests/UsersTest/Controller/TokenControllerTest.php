<?php

namespace UsersTest\Controller;

use ApplicationTest\Controller\BaseControllerTest;
use Exception;
use Users\Controller\TokenController;

/**
 * @group token
 */
class TokenControllerTest extends BaseControllerTest
{
    /**
     * @throws Exception
     */
    public function testIndexActionCanBeAccessed()
    {
        $configApp = $this->getConfigApp();
        $this->setRequestHeadersParametersToken($configApp);

        $this->dispatch('/token');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('users');
        $this->assertControllerName(TokenController::class);
        $this->assertControllerClass('TokenController');
        $this->assertMatchedRouteName('token');
    }

    /**
     * @return void
     * @throws Exception
     * @group token
     */
    public function testIndexActionCannotBeAccessedWithout(): void
    {
        $configApp = $this->getConfigApp();
        $configApp['app_key'] = '123';
        $this->setRequestHeadersParametersToken($configApp);

        $this->dispatch('/token');

        $this->assertResponseStatusCode(401);
        $this->assertModuleName('users');
        $this->assertControllerName(TokenController::class); // as specified in router's controller name alias
        $this->assertControllerClass('TokenController');
        $this->assertMatchedRouteName('token');
    }

    /**
     * @return void
     * @throws Exception
     * @group token
     */
    public function testIndexActionCannotBeAccessedWithWrongUsername(): void
    {
        $configApp = $this->getConfigApp();
        $configApp['app_username'] = '123';
        $this->setRequestHeadersParametersToken($configApp);

        $this->dispatch('/token');

        $this->assertResponseStatusCode(401);
        $this->assertModuleName('users');
        $this->assertControllerName(TokenController::class); // as specified in router's controller name alias
        $this->assertControllerClass('TokenController');
        $this->assertMatchedRouteName('token');
    }

    /**
     * @return void
     * @throws Exception
     * @group token
     */
    public function testIndexActionCannotBeAccessedWithWrongPassword(): void
    {
        $configApp = $this->getConfigApp();
        $configApp['app_password'] = '123';
        $this->setRequestHeadersParametersToken($configApp);

        $this->dispatch('/token');

        $this->assertResponseStatusCode(401);
        $this->assertModuleName('users');
        $this->assertControllerName(TokenController::class); // as specified in router's controller name alias
        $this->assertControllerClass('TokenController');
        $this->assertMatchedRouteName('token');
    }

    /**
     * @throws Exception
     * @group token
     */
    public function testGenerateToken()
    {
        $configApp = $this->getConfigApp();
        $token = $this->generateToken($configApp);

        $this->assertNotNull($token);
    }

    /**
     * @throws Exception
     * @group token
     */
    public function testTokenMethodReturn()
    {
        $configApp = $this->getConfigApp();
        $token = $this->generateToken($configApp, true);

        $result = $this->getResult();

        $this->assertNotNull($token);
        $this->assertTrue(isset($result['status']));
        $this->assertTrue(isset($result['result']['token']));
    }
}
