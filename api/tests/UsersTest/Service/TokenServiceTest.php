<?php

namespace UsersTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Users\Service\TokenService;
use Users\Values\TokenPost;

/**
 * @group token
 * @group token_service
 * @group user
 */
class TokenServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var TokenService
     */
    protected TokenService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  TokenService $this- >service */
        $this->service = $this->getApplicationServiceLocator()->get(TokenService::class);
    }

    /**
     * @group database
     * @throws Exception
     */
    public function testAppKeyException()
    {
        $this->expectExceptionMessage(TokenService::EXCEPTION_APPKEY);
        $appKey = '';

        $pars = [
            'username' => 'username',
            'password' => 'password',
        ];
        $tokenPost = new TokenPost($pars, $appKey);
        $this->service->checkUser($tokenPost);
    }

    /**
     * @group database
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testAppKeyUsernamePassword()
    {
        $this->expectExceptionMessage(TokenService::EXCEPTION_INVALID_USER);

        $pars = $this->getDefaultPars();
        $appKey = $this->getHeaderAppKey();

        $tokenPost = new TokenPost($pars, $appKey);
        $this->service->checkUser($tokenPost);
    }

    /**
     * @group database
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testAppKeyPassword()
    {
        $this->expectExceptionMessage(TokenService::EXCEPTION_INVALID_USER);

        $pars = $this->getDefaultPars();
        $pars['username'] = 'app-access';

        $appKey = $this->getHeaderAppKey();

        $tokenPost = new TokenPost($pars, $appKey);
        $this->service->checkUser($tokenPost);
    }

    /**
     * @return string[]
     */
    public function getDefaultPars(): array
    {
        return [
            'username' => 'username',
            'password' => 'password',
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getHeaderAppKey()
    {
        $config = $this->getApplicationServiceLocator()->get('config');
        return $config['app']['app_key'];
    }

}
