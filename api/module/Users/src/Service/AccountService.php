<?php

namespace Users\Service;

use Application\Service\BaseService;
use Exception;
use Laminas\Json\Json;
use RedisException;
use Users\Entity\User;
use Users\Values\RecoverPost;

/**
 * Class AccountService
 * @package Users\Service
 */
class AccountService extends BaseService
{
    const EXCEPTION_ID_NOT_SET = 'User id not set!';
    const EXCEPTION_NOT_FOUND = 'User not found!';

    private ?User $user;
    private ?UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @throws Exception
     */
    public function getResumeData(): array
    {
        $this->loadUserStorage();

        return [
            'user' => $this->user->toArray(),
            'preferences' => [] // @todo get preferences
        ];
    }

    /**
     * @throws Exception
     */
    public function startRecoverPassword(RecoverPost $recoverPost)
    {
        $email = $recoverPost->getEmail();

        $this->sendToPubRecoverEmail($email);
        // pub sub user data to generate and send code

        return $email;
    }

    /**
     * @throws RedisException
     */
    public function sendToPubRecoverEmail(string $mail)
    {
        $message = Json::encode(['email' => $mail]);
        $this->getStorage()->publish('recover', $message);
    }

    /**
     * @throws Exception
     */
    public function seachUserEmail(string $email): ?int
    {
        $this->user = $this->userService->getUserByEmail($email);

        echo $this->user->getEmail();
        echo "\n------\n";

        if (!$this->user) {
            throw new Exception('User not found.');
        }

        return $this->user->getId();
    }

    /**
     * @throws RedisException
     */
    public function sendToPubRecover(User $user)
    {
        $userId = $user->getId();

        $data = [
            'user_id' => $userId
        ];

        $message = Json::encode($data);
        $this->getStorage()->publish('recover', $message);
    }

    /**
     * @throws Exception
     */
    public function clearAccessData()
    {
        $this->loadUser();

        $userKey = 'user_' . $this->user->getId();
        $this->deleteStoreItem($userKey);
    }

    /**
     * @throws Exception
     */
    public function loadUser()
    {
        if ($this->userId == 0) {
            throw new Exception(self::EXCEPTION_ID_NOT_SET);
        }

        $repository = $this->em->getRepository(User::class);
        $this->user = $repository->find(['id' => $this->userId]);

        if (!$this->user) {
            throw new Exception(self::EXCEPTION_NOT_FOUND);
        }
    }

    /**
     * @throws Exception
     */
    public function loadUserStorage()
    {
        if ($this->userId == 0) {
            throw new Exception(self::EXCEPTION_ID_NOT_SET);
        }

        $this->user = $this->getStoreItem('user_' . $this->userId, true);

        if (!$this->user) {
            throw new Exception(self::EXCEPTION_NOT_FOUND);
        }
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
