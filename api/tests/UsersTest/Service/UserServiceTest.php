<?php

namespace UsersTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Users\Entity\User;
use Users\Exception\UserException;
use Users\Service\UserService;
use Users\Values\UserPost;

/**
 * @group user
 */
class UserServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var UserService
     */
    protected UserService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  UserService $this- >service */
        $this->service = $this->getApplicationServiceLocator()->get(UserService::class);
    }

    /**
     * @group database
     */
    public function testGetUsers()
    {
        $users = $this->service->getUsers();

        $this->assertIsArray($users);
    }

    /**
     * @group database
     * @group provider
     *
     */
    public function testGetItem()
    {
        $userItem = $this->service->getItem(1);

        $this->assertIsArray($userItem);
        $this->assertTrue(isset($userItem['name']));
        $this->assertTrue($userItem['user_name'] == 'app-access');
    }


    /**
     * @group database
     */
    public function testGetItemFail()
    {
        $userItem = $this->service->getItem(-1);

        $this->assertIsArray($userItem);
        $this->assertFalse(isset($userItem['name']));
    }

    /**
     * @group database
     * @group create
     * @group update
     *
     * @throws ORMException
     * @throws Exception
     * @throws UserException
     * @dataProvider provideUserData
     */
    public function testCreate(array $pars)
    {
        $userPost = new UserPost($pars);

        /** @var User $user */
        $user = $this->service->getUserByEmail($pars['email']);
        if ($user) {
            $this->service->delete($user->getId());
        }

        $status = $this->service->create($userPost);
        $this->assertTrue($status);
    }

    /**
     * @group database
     * @group create
     *
     * @depends      testCreate
     * @throws ORMException
     * @throws Exception|UserException
     * @dataProvider provideUserData
     */
    public function testCreateWithSamePars(array $pars)
    {
        $this->expectExceptionMessage('Email is register for other user!');

        $userPost = new UserPost($pars);
        $status = $this->service->create($userPost);
        $this->assertTrue($status);
    }

    /**
     * @group database
     *
     * @throws ORMException|Exception|UserException
     * @dataProvider provideUserData
     */
    public function testCreateNameException(array $pars)
    {
        $this->expectExceptionMessage('Item name not sent!');

        unset($pars['name']);
        $userPost = new UserPost($pars);

        $this->service->create($userPost);
    }

    /**
     * @group database
     *
     * @throws ORMException|Exception|UserException
     * @dataProvider provideUserData
     */
    public function testCreateUserNameException(array $pars)
    {
        $this->expectExceptionMessage('Item user_name not sent!');

        unset($pars['user_name']);
        $userPost = new UserPost($pars);

        $this->service->create($userPost);
    }

    /**
     * @group database
     *
     * @depends      testCreate
     * @dataProvider provideUserData
     * @throws ORMException
     * @throws Exception
     * @throws UserException
     */
    public function testCreateInvalidEmail(array $pars)
    {
        $this->expectExceptionMessage('Email is not valid!');

        $pars['email'] = 'email@site';
        $userPost = new UserPost($pars);

        $this->service->create($userPost);
    }

    /**
     * @group update
     *
     * @depends      testCreate
     * @dataProvider provideUserData
     * @param array $pars
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws UserException
     */
    public function testUpdate(array $pars)
    {
        /** @var User $user */
        $user = $this->service->getUserByEmail($pars['email']);
        $pars = $user->toArray();

        $userPost = new UserPost($pars);
        $status = $this->service->update($userPost);
        $this->assertTrue($status);
    }

    /**
     * @group update
     *
     * @depends      testCreate
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException|UserException
     */
    public function testUpdateEntityNotFound()
    {
        $this->expectExceptionMessage('Entity not found!');

        $pars = [
            'id' => -1,
            'name' => 'Fernando Caruso',
            'user_name' => 'fernando-caruso',
            'email' => 'fernando-caruso@mail.com',
        ];

        $userPost = new UserPost($pars);

        $this->service->update($userPost);
    }

    /**
     * @group update
     *
     * @depends      testCreate
     * @throws ORMException|Exception|UserException
     * @dataProvider provideUserData
     */
    public function testUpdateNameException(array $pars)
    {
        $this->expectExceptionMessage('Item name not sent!');

        /** @var User $user */
        $user = $this->service->getUserByEmail($pars['email']);
        $pars = $user->toArray();

        unset($pars['name']);
        $userPost = new UserPost($pars);

        $this->service->update($userPost);
    }

    /**
     * @group update
     *
     * @depends      testCreate
     * @throws ORMException|Exception|UserException
     * @dataProvider provideUserData
     */
    public function testUpdateUserNameException(array $pars)
    {
        $this->expectExceptionMessage('Item user_name not sent!');

        /** @var User $user */
        $user = $this->service->getUserByEmail($pars['email']);
        $pars = $user->toArray();

        unset($pars['user_name']);
        $userPost = new UserPost($pars);

        $this->service->update($userPost);
    }

    /**
     * @group update
     *
     * @depends      testCreate
     * @throws ORMException|Exception|UserException
     * @dataProvider provideUserData
     */
    public function testUpdateInvalidEmailException(array $pars)
    {
        $this->expectExceptionMessage('Email is register for other user!');

        $userApp = $this->service->getItem(1);

        /** @var User $user */
        $user = $this->service->getUserByEmail($pars['email']);
        $pars = $user->toArray();

        $pars['email'] = $userApp['email'];
        $userPost = new UserPost($pars);

        $this->service->update($userPost);
    }

    /**
     * @group database
     *
     * @depends      testCreate
     * @dataProvider provideUserData
     * @throws ORMException
     * @throws Exception
     * @throws UserException
     */
    public function testUpdateInvalidEmail(array $pars)
    {
        $this->expectExceptionMessage('Email is not valid!');

        /** @var User $user */
        $user = $this->service->getUserByEmail($pars['email']);
        $pars = $user->toArray();

        $pars['email'] = 'email@site';
        $userPost = new UserPost($pars);

        $this->service->update($userPost);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testDeleteGoldenRule()
    {
        $status = $this->service->delete(1);
        $this->assertFalse($status);
    }

    /**
     * @throws \Exception
     */
    public function testCreatePassword()
    {
        $password = '1234567';
        $passwordHash = $this->service->createPassword($password);

        $check = password_verify($password, $passwordHash);
        $this->assertTrue($check);
    }

    /**
     * @group update
     *
     * @depends      testCreate
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testSearch(array $pars)
    {
        $str = $pars['name'];
        $result = $this->service->search($str);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * @group update
     *
     * @throws Exception
     */
    public function testSearchWithShortString()
    {
        $str = 'Lu';
        $result = $this->service->search($str);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
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
