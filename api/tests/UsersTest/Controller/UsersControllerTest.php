<?php

namespace UsersTest\Controller;

use ApplicationTest\Controller\BaseControllerTest;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Users\Controller\UsersController;
use Users\Service\UserService;

/**
 * @group controllers
 * @group users
 */
class UsersControllerTest extends BaseControllerTest
{
    /**
     * @throws Exception
     */
    public function testGetListUsersWithoutAuth()
    {
        $this->dispatch('/users', 'GET');

        $this->assertResponseStatusCode(401);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @throws Exception
     */
    public function testGetListUsers()
    {
        $this->getRequestHeadersJwt();

        $this->dispatch('/users', 'GET');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @throws Exception
     */
    public function testGetUser()
    {
        $this->getRequestHeadersJwt();

        $this->dispatch('/users/1', 'GET');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @throws Exception
     */
    public function testGetUserNotFound()
    {
        $this->getRequestHeadersJwt();

        $this->dispatch('/users/-1', 'GET');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update
     *
     * @param array $pars
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testCreateUser(array $pars)
    {
        $this->removeUserByEmail($pars['email']);

        $this->getRequestHeadersJwt();

        $this->dispatch('/users', 'POST', $pars);

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update
     *
     * @param array $pars
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testCreateUserException(array $pars)
    {
        $this->getRequestHeadersJwt();

        $this->dispatch('/users', 'POST', $pars);

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update
     *
     * @param array $pars
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testCreateUserNameException(array $pars)
    {
        $this->getRequestHeadersJwt();

        $pars['name'] = 'Fernando';
        $this->dispatch('/users', 'POST', $pars);

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update
     *
     * @param array $pars
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testCreateUserInvalidEmail(array $pars)
    {
        $this->getRequestHeadersJwt();

        $pars['email'] = 'fa@mail';
        $this->dispatch('/users', 'POST', $pars);

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update
     *
     * @depends testCreateUser
     * @param   array $pars
     * @throws  Exception
     * @dataProvider provideUserData
     */
    public function testCreateUserIsRegisteredEmail(array $pars)
    {
        $this->getRequestHeadersJwt();
        $this->dispatch('/users', 'POST', $pars);

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update_user
     *
     * @depends      testCreateUser
     * @param array $pars
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testUpdateUser(array $pars)
    {
        /** @var  UserService $this- >service */
        $service = $this->getApplicationServiceLocator()->get(UserService::class);
        $user = $service->getUserByEmail($pars['email']);

        $this->getRequestHeadersJwt();
        $this->dispatch('/users/' . $user->getId(), 'PUT', $pars);

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update
     *
     * @depends      testCreateUser
     * @param array $pars
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testUpdateUserInvalidEmail(array $pars)
    {
        /** @var  UserService $this- >service */
        $service = $this->getApplicationServiceLocator()->get(UserService::class);
        $user = $service->getUserByEmail($pars['email']);

        $this->getRequestHeadersJwt();

        $pars['email'] = 'nome@email';
        $this->dispatch('/users/' . $user->getId(), 'PUT', $pars);

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group update
     *
     * @param array $pars
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @dataProvider provideUserData
     */
    public function testUpdateUserNotFound(array $pars)
    {
        /** @var  UserService $this- >service */
        $service = $this->getApplicationServiceLocator()->get(UserService::class);
        $user = $service->getUserByEmail($pars['email']);

        $this->getRequestHeadersJwt();
        $this->dispatch('/users/-1', 'PUT', $pars);

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @depends      testCreateUser
     * @param array $pars
     * @throws  ContainerExceptionInterface
     * @throws  NotFoundExceptionInterface
     * @throws  Exception
     * @dataProvider provideUserData
     */
    public function testDeleteUser(array $pars)
    {
        /** @var  UserService $this- >service */
        $service = $this->getApplicationServiceLocator()->get(UserService::class);
        $user = $service->getUserByEmail($pars['email']);

        $this->getRequestHeadersJwt();

        $this->dispatch('/users/' . $user->getId(), 'DELETE', $pars);

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDeleteUserGoldenRule(): void
    {
        $this->getRequestHeadersJwt();

        $goldenUserId = 1;
        $this->dispatch('/users/' . $goldenUserId, 'DELETE');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users');
    }

    /**
     * @group password
     *
     * @return void
     * @throws Exception
     */
    public function testPasswordTest(): void
    {
        $postData = [
            'password' => 'Ch@M4sTer!)@(#'
        ];

        $this->configurePostJson($postData);
        $this->dispatch('/users/password-test');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users-password-db');
    }

    /**
     * @group password
     *
     * @return void
     * @throws Exception
     */
    public function testPasswordTestEmpty(): void
    {
        $postData = [
            'password' => ''
        ];

        $this->configurePostJson($postData);
        $this->dispatch('/users/password-test');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users-password-db');
    }

    /**
     * @group password
     *
     * @return void
     * @throws Exception
     */
    public function testPasswordTestException(): void
    {
        $headers = new Headers();
        $headers->addHeaderLine('Content-Type', 'application/json');

        /** @var Request $request */
        $request = $this->getRequest();
        $request->setHeaders($headers);

        $postData = [
            'password1' => 'Ch@M4sTer!)@(#'
        ];
        $request->setMethod('POST')->setContent(Json::encode($postData));


        $this->dispatch('/users/password-test');

        $this->assertResponseStatusCode(400);
        $this->assertModuleName('users');
        $this->assertControllerName(UsersController::class);
        $this->assertControllerClass('UsersController');
        $this->assertMatchedRouteName('users-password-db');
    }

    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    public function removeUserByEmail($email)
    {
        /** @var  UserService $this- >service */
        $service = $this->getApplicationServiceLocator()->get(UserService::class);
        $user = $service->getUserByEmail($email);
        if ($user) {
            $service->delete($user->getId());
        }
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
