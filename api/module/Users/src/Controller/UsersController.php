<?php

namespace Users\Controller;

use Application\Controller\ApiController;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Laminas\View\Model\JsonModel;
use RedisException;
use Users\Exception\UserException;
use Users\Service\UserService;
use Users\Values\UserPost;

/**
 * Class UsersController
 * @package Users\Controller
 */
class UsersController extends ApiController
{
    /**
     * @var UserService $service
     */
    protected UserService $service;

    /**
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * @return JsonModel
     */
    public function getList(): JsonModel
    {
        $this->preLoadMethod();

        $list = $this->service->getUsers();

        $data = [
            'users' => $list,
            'method' => 'get'
        ];
        return $this->createResponse($data);
    }

    /**
     * @param $id
     * @return JsonModel
     */
    public function get($id): JsonModel
    {
        $item = $this->service->getItem((int)$id);
        if (empty($item)) {
            $this->httpStatusCode = 400;
        }
        $data = [
            'user' => $item,
            'method' => 'get'
        ];
        return $this->createResponse($data);
    }

    /**
     * @param $data
     * @return JsonModel
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create($data): JsonModel
    {
        try {
            $userPost = new UserPost($data);
            $this->service->create($userPost);

            $data = [
                'id' => $this->service->getId(),
                'user' => $userPost->getData(),
                'action' => 'create'
            ];
        } catch (UserException $e) {
            $this->httpStatusCode = 400;

            $logMessage = $e->customMessage();

            $data = [
                'message' => $e->customMessage(),
                'id' => 0,
                'action' => 'create'
            ];
        } catch (\Doctrine\DBAL\Exception $e) {
            $this->httpStatusCode = 400;

            $data = [
                'message' => $e->getMessage(),
                'id' => 0,
                'action' => 'create'
            ];
        }

        return $this->createResponse($data);
    }

    /**
     * @param $id
     * @param $data
     * @return JsonModel
     */
    public function update($id, $data): JsonModel
    {
        try {
            $data['id'] = $id;
            $userPost = new UserPost($data);

            $status = $this->service->update($userPost);

            $this->httpStatusCode = ($status ? $this->httpStatusCode : 400);

            $data = [
                'item' => $userPost->getData(),
                'action' => 'update'
            ];

        } catch (Exception $e) {

            $this->httpStatusCode = 400;
            if ($e->getMessage() == 'Entity not found!') {
                $this->httpStatusCode = 404;
            }

            $data = [
                'message' => $e->getMessage(),
                'id' => 0,
                'action' => 'create'
            ];
        }

        return $this->createResponse($data);
    }

    /**
     * @param $id
     * @return JsonModel
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RedisException
     */
    public function delete($id): JsonModel
    {
        $status = $this->service->delete((int)$id);
        if (!$status) {
            $this->httpStatusCode = 400;
        }

        $data = [
            'id' => $id,
            'action' => 'delete',
        ];
        return $this->createResponse($data);
    }

    /**
     * @return JsonModel
     */
    public function passwordDbAction(): JsonModel
    {
        try {
            $pars = $this->getJsonParameters();

            $password = $pars['password'];
            $passwordHash = $this->service->createPassword($password);

            $data = [
                'password' => $password,
                'password_hash' => $passwordHash,
            ];
        } catch (Exception $e) {
            $this->httpStatusCode = 400;

            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    /**
     * @return void
     */
    public function preLoadMethod(): void
    {
        $userId = $this->getPayload()->getSub();
        $this->service->setUserId($userId);
    }
}
