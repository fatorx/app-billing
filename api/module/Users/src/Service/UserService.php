<?php

namespace Users\Service;

use Application\Service\BaseService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Laminas\Hydrator\ClassMethodsHydrator;
use RedisException;
use Users\Entity\User;
use Users\Values\RecoverPost;
use Users\Values\UserPost;

/**
 * Class UserService
 * @package Users\Service
 */
class UserService extends BaseService
{
    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var string
     */
    private string $entity;

    /**
     * @var array
     */
    protected array $users = [];

    /**
     * @var User
     */
    protected User $userEntity;

    /**
     * TagService constructor.
     */
    public function __construct()
    {
        $this->entity = User::class;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        $usersRepository = $this->em->getRepository(User::class);
        $list = $usersRepository->findAll();

        /** @todo Refactoring to Collection */
        return array_map(function ($item) {
            return $item->toArray();
        }, $list);
    }

    /**
     * @return array
     */
    public function getListUsers(): array
    {
        $usersRepository = $this->em->getRepository(User::class);
        return $usersRepository->findAll();
    }

    /**
     * @param int $id
     * @return array
     */
    public function getItem(int $id): array
    {
        $user = $this->em->getRepository($this->entity)
            ->findOneBy(['id' => $id]);

        if ($user) {
            return $user->toArray();
        }

        return [];
    }


    /**
     * @param UserPost $userPost
     * @return bool
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(UserPost $userPost): bool
    {
        $pars = $this->validCreate($userPost->getData());

        $pars['active'] = 1;
        $user = new $this->entity($pars);
        $this->em->persist($user);
        $this->em->flush();

        $this->id = $user->getId();

        $this->status = ($this->id > 0);
        return $this->status;
    }

    /**
     * @param array $pars
     * @return array
     * @throws Exception
     */
    public function validCreate(array $pars): array
    {
        $email = $pars['email'];
        $user = $this->getUserByEmail($email);

        if ($user) {
            throw new Exception('Email is register for other user!');
        }

        return $pars;
    }


    /**
     * @param UserPost $userPost
     * @return bool
     * @throws Exception
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws RedisException
     */
    public function update(UserPost $userPost): bool
    {
        $pars = $userPost->getData();
        $id = $pars['id'];

        /** @var User|null $entityRef */
        $entityRef = $this->em->find($this->entity, $id);
        if (!$entityRef) {
            throw new Exception('Entity not found!');
        }

        $pars = $this->validUpdate($pars);

        $hydrator = new ClassMethodsHydrator();
        $hydrator->hydrate($pars, $entityRef);

        $entityRef->setUpdatedAt();
        $this->em->persist($entityRef);
        $this->em->flush();

        $key = 'user_' . $entityRef->getId();
        $this->setStoreItem($key, $entityRef, true);

        return true;
    }

    /**
     * @param array $pars
     * @return array
     * @throws Exception
     */
    public function validUpdate(array $pars): array
    {
        $user = $this->getUserByEmail($pars['email']);
        if ($user && $user->getId() != $pars['id']) {
            throw new Exception('Email is register for other user!');
        }

        return $pars;
    }

    /**
     * @param string $email
     * @return ?User
     */
    public function getUserByEmail(string $email): ?User
    {
        $repository = $this->em->getRepository($this->entity);
        $user = $repository->findOneBy(['email' => $email]);

        if ($user) {
            return $user;
        }

        return null;
    }


    /**
     * @param int $id
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws RedisException
     */
    public function delete(int $id): bool
    {
        if ($id == 1) { // Golden rule!
            return false;
        }

        $repository = $this->em->getRepository($this->entity);
        $entity = $repository->findOneBy(['id' => $id]);

        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();

            $this->deleteStoreItem('user_' . $id);
        }

        return true;
    }

    /**
     * @param string $password
     * @return string
     * @throws \Exception
     */
    public function createPassword(string $password): string
    {
        if ($password == null) {
            throw new Exception("Password isn't empty!");
        }

        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * @param $str
     * @return array
     * @throws Exception
     */
    public function search($str): array
    {
        if (strlen($str) < 3) {
            $this->status = false;
            return [];
        }

        $sql = ' SELECT id, name, username, phone, email, picture, ';
        $sql .= ' bio, created_at, updated_at, origin ';
        $sql .= ' FROM users';
        $sql .= ' WHERE (name LIKE "%' . $str . '%" ';
        $sql .= ' OR username LIKE "%' . $str . '%") ';

        $rs = $this->executeSql($sql, 'all');

        foreach ($rs as $item) {
            $this->users[] = new User($item);
        }

        return $this->users;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
