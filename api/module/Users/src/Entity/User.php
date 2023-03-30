<?php

namespace Users\Entity;

use DateTime;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;


#[Table(name: "users")]
#[Entity]
class User
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int|null $id = 0;

    #[Column(name:"name", type: "string", length: 90,  nullable: false)]
    protected string $name;

    #[Column(name:"username", type: "string", length: 90,  nullable: true)]
    protected string $userName;

    #[Column(name:"bio", type: "string", length: 50,  nullable: true)]
    protected string $bio;

    #[Column(name:"picture", type: "string", length: 50,  nullable: true)]
    protected string $picture;

    #[Column(name:"email", type: "string", length: 70,  nullable: true)]
    protected string $email;

    #[Column(name:"password", type: "string", length: 100,  nullable: true)]
    protected string $password;

    #[Column(name:"created_at", type: "datetime", nullable:true)]
    protected Datetime $createdAt;

    #[Column(name:"updated_at", type: "datetime", nullable:true)]
    protected Datetime $updatedAt;

    #[Column(name:"active", type: "string", length: 90,  nullable: false)]
    protected ?string $active = null;

    /**
     * Cartao constructor.
     * @param array $input
     */
    public function __construct(array $input = [])
    {
        if (!empty($input)) {
            $this->exchangeArray($input);
            $this->createdAt = new Datetime();
            $this->updatedAt = new Datetime();
        }
    }

    /**
     * @param array $input
     */
    public function exchangeArray(array $input): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
        $hydrator->hydrate($input, $this);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $extractData =  (new ClassMethodsHydrator(true))->extract($this);

        unset($extractData['password'], $extractData['created_at'],
              $extractData['updated_at'] );

        return $extractData;
    }

    /**
     * Get the value of id
     */
    public function getId(): int|null
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param $id
     * @return  self
     */
    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param $name
     * @return  self
     */
    public function setName($name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Set the value of username
     *
     * @param string $userName
     * @return  self
     */
    public function setUserName(string $userName): static
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * Get the value of slug email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of slug email
     *
     * @param $email
     * @return  self
     */
    public function setEmail($email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @param $password
     * @return  self
     */
    public function setPassword($password): static
    {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $this->password = $password;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getCreatedAtFormat(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    /**
     * @return self
     */
    public function setCreatedAt(): User
    {
        $this->createdAt = (new DateTime);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAtFormat(): string
    {
        return $this->updatedAt->format('Y-m-d H:i:s');
    }

    /**
     * @return self
     */
    public function setUpdatedAt(): User
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * Get the value of active
     */
    public function getActive(): string
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @param $active
     * @return  self
     */
    public function setActive($active): static
    {
        $this->active = $active;
        return $this;
    }
}
