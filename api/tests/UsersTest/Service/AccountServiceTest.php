<?php

namespace UsersTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Users\Entity\User;
use Users\Service\AccountService;
use Users\Values\RecoverPost;

/**
 * @group account
 * @group account_service
 */
class AccountServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var AccountService
     */
    protected AccountService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  AccountService $this- >service */
        $this->service = $this->getApplicationServiceLocator()->get(AccountService::class);
    }

    /**
     * @throws Exception
     */
    public function testLoadUserExceptionId()
    {
        $this->expectExceptionMessage(AccountService::EXCEPTION_ID_NOT_SET);

        $this->service->loadUser();
    }

    /**
     * @throws Exception
     */
    public function testLoadUserNotFound()
    {
        $this->expectExceptionMessage(AccountService::EXCEPTION_NOT_FOUND);

        $this->service->setUserId(-1);
        $this->service->loadUser();
    }

    /**
     * @throws Exception
     */
    public function testLoadUser()
    {
        $id = 1;
        $this->service->setUserId($id);
        $this->service->loadUser();

        $user = $this->service->getUser();

        $this->assertInstanceOf(User::class, $user, 'Instance different of the target');
        $this->assertEquals($user->getId(), $id);
    }

    /**
     * @throws Exception
     */
    public function testLoadUserStorageExceptionId()
    {
        $this->expectExceptionMessage(AccountService::EXCEPTION_ID_NOT_SET);

        $this->service->loadUserStorage();
    }

    /**
     * @group error
     * @throws Exception
     */
    public function testLoadUserStorageNotFound()
    {
        $this->expectExceptionMessage(AccountService::EXCEPTION_NOT_FOUND);

        $this->service->setUserId(-1);

        $this->service->loadUserStorage();
    }

    /**
     * @throws Exception
     */
    public function testLoadUserStorage()
    {
        $id = 1;
        $this->service->setUserId($id);
        $this->service->loadUserStorage();

        $user = $this->service->getUser();

        $this->assertInstanceOf(User::class, $user, 'Instance different of the target');
        $this->assertEquals($user->getId(), $id);
    }

    /**
     * @throws Exception
     */
    public function testGetResumeData()
    {
        $id = 1;
        $this->service->setUserId($id);

        $userData = $this->service->getResumeData();

        $this->assertIsArray($userData, 'Instance different of the target');
        $this->assertNotEmpty($userData);
    }

    /**
     * @throws Exception
     */
    public function testRecoverPassword()
    {
        $userData = $this->getEmail();
        $recoverPost = new RecoverPost($userData);

        $userData = $this->service->startRecoverPassword($recoverPost);

        $this->assertNotEmpty($userData);
    }

    public function getEmail(): array
    {
        return [
            'email' => 'machado-assis@abl.com.br'
        ];
    }

    /**
     * @return string[][]
     */
    public function provideUserData(): array
    {
        return [
            'User Luí (luiz.gonzaga@mpb.com)' => [
                'data' => [
                    'name' => 'Luiz Gonzaga do Nascimento',
                    'user_name' => 'luiz-gonzaga',
                    'email' => 'luiz.gonzaga@mpb.com',
                    'password' => 'Eh4AllThePeople/_\1930',
                    'active' => 1,
                ]
            ],
            'User Raulzito (raul.seixas@mpb.com)' => [
                'data' => [
                    'name' => 'Raulzito Santos Seixas',
                    'user_name' => 'raulzito',
                    'email' => 'raul.seixas@mpb.com',
                    'password' => 'Eh4AllThePeople/_\1930',
                    'active' => 1,
                ]
            ]
        ];
    }
}
