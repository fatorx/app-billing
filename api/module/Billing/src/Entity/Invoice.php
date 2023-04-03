<?php

namespace Billing\Entity;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Exception;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Laminas\Validator\Date;

#[Table(name: "billings")]
#[Entity]
class Invoice
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int|null $id = 0;

    #[Column(name:"name", type: "string", length: 90,  nullable: true)]
    protected string $name;

    #[Column(name:"government_id", type: "string", length: 11,  nullable: false)]
    protected string $governmentId;

    #[Column(name:"email", type: "string", length: 90,  nullable: true)]
    protected string $email;

    #[Column(name:"amount", type: "decimal", precision: 5)]
    protected string $amount;

    #[Column(name:"due_date", type: "date", nullable: false)]
    protected DateTime $dueDate;

    #[Column(name:"status", type: "integer", nullable: false)]
    protected int $status = 0;

    #[Column(name:"debt_id", type: "integer", nullable: false)]
    protected int $debtId = 0;

    #[Column(name:"created_at", type: "datetime", nullable:false)]
    protected Datetime $createdAt;

    #[Column(name:"updated_at", type: "datetime", nullable:false)]
    protected Datetime $updatedAt;

    /**
     *
     * @param array $input
     * @throws Exception
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
     * @throws Exception
     */
    public function exchangeArray(array $input): void
    {
        $hydrator = new ClassMethodsHydrator(false);
        $hydrator->setNamingStrategy(new UnderscoreNamingStrategy());

        $dueDate = new DateTime($input['due_date']);
        unset($input['due_date']);

        $hydrator->hydrate($input, $this);
        $this->setDueDate($dueDate);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $extractData =  (new ClassMethodsHydrator(true))->extract($this);

        unset($extractData['created_at'], $extractData['updated_at'] );
        return $extractData;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGovernmentId(): string
    {
        return $this->governmentId;
    }

    /**
     * @param string $governmentId
     */
    public function setGovernmentId(string $governmentId): void
    {
        $this->governmentId = $governmentId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param bool $format
     * @return string
     */
    public function getAmount(bool $format = false): string
    {
        if ($format) {
            return  number_format($this->amount,2,",",".");
        }

        return $this->amount;
    }

    /**
     * @param string $amount
     */
    public function setAmount(string $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return DateTime
     */
    public function getDueDate(): DateTime
    {
        return $this->dueDate;
    }

    /**
     * @param DateTime $dueDate
     */
    public function setDueDate(DateTime $dueDate): void
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getDebtId(): int
    {
        return $this->debtId;
    }

    /**
     * @param int $debtId
     */
    public function setDebtId(int $debtId): void
    {
        $this->debtId = $debtId;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
