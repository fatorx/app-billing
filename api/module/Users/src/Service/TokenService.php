<?php

namespace Users\Service;

use Application\Service\BaseService;
use Datetime;
use Exception;
use Users\Entity\User;
use Users\Values\TokenPost;

/**
 * Class TokenService
 * @package Users\Service
 */
class TokenService extends BaseService
{
    const EXCEPTION_APPKEY = 'App key invalid.';
    const EXCEPTION_INVALID_ACCESS = 'Username or password invalid.';
    const EXCEPTION_INVALID_USER = 'Username or password invalid.';

    const EXPIRATION_MINUTES = 1;
    const AUDIENCE = 'app';

    private string $entity;

    /**
     * TokenService constructor.
     */
    public function __construct()
    {
        $this->entity = User::class;
    }

    /**
     * @param TokenPost $tokenPost
     * @param string $appKey
     * @return User|false
     * @throws Exception
     */
    public function checkUser(TokenPost $tokenPost): User|false
    {
        $pars = $tokenPost->getData();

        $configAppKey = $this->config['app_key'];
        $appKey = $pars['app_key'];

        if ($configAppKey != $appKey) {
            throw new Exception(self::EXCEPTION_APPKEY);
        }

        $repository = $this->em->getRepository($this->entity);

        /** @var User|false $user */
        $user = $repository->findOneBy(['userName' => $pars['username']]);

        if (!$user) {
            throw new Exception(self::EXCEPTION_INVALID_ACCESS);
        }

        $passwordHash = $user->getPassword();
        $verify = password_verify($pars['password'], $passwordHash);
        if (!$verify) {
            throw new Exception(self::EXCEPTION_INVALID_USER);
        }

        $key = 'user_' . $user->getId();
        $this->setStoreItem($key, $user, true);

        return $user;
    }

    /**
     * @param User $user
     * @return array
     */
    public function getPayloadData(User $user): array
    {
        return [
            'sub' => $user->getId(),
            'name' => $user->getName(),
            'admin' => false,
            'issued_at' => $this->getIssuedAt(),
            'expiration' => $this->getExpiration(),
            'audience' => $this->getAudience(),
        ];
    }

    /**
     * @return int
     */
    public function getExpiration(): int
    {
        $dateTime = new Datetime();
        $dateTime->modify('+' . self::EXPIRATION_MINUTES . ' minutes');
        return $dateTime->getTimestamp();
    }

    /**
     * @return int
     */
    public function getIssuedAt(): int
    {
        $dateTime = new Datetime();
        return $dateTime->getTimestamp();
    }

    /**
     * @return string
     */
    public function getAudience(): string
    {
        return self::AUDIENCE;
    }
}
