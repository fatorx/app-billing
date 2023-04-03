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

#[Table(name: "payments")]
#[Entity]
class Payment
{
    const STATUS_NOT_PROCESSED = 0;

    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue]
    private int|null $id = 0;

    #[Column(name: "debt_id", type: "integer", nullable: false)]
    protected int $debtId = 0;

    #[Column(name: "paid_at", type: "datetime", nullable: false)]
    protected DateTime $paidAt;

    #[Column(name: "paid_amount", type: "decimal", precision: 5)]
    protected string $paidAmount;

    #[Column(name: "paid_by", type: "string", length: 90, nullable: true)]
    protected string $paidBy;

    #[Column(name: "billing_id", type: "integer", nullable: false)]
    protected int $billingId = 0;

    #[Column(name: "status", type: "integer", nullable: false)]
    protected int $status = self::STATUS_NOT_PROCESSED;

    #[Column(name: "created_at", type: "datetime", nullable: false)]
    protected Datetime $createdAt;

    #[Column(name: "updated_at", type: "datetime", nullable: false)]
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

        $hydrator->hydrate($input, $this);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $extractData = (new ClassMethodsHydrator(true))->extract($this);

        unset($extractData['created_at'], $extractData['updated_at']);
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
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
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
    public function getPaidAt(): DateTime
    {
        return $this->paidAt;
    }

    /**
     * @param DateTime $paidAt
     */
    public function setPaidAt(DateTime $paidAt): void
    {
        $this->paidAt = $paidAt;
    }

    /**
     * @return string
     */
    public function getPaidAmount(): string
    {
        return $this->paidAmount;
    }

    /**
     * @param float $paidAmount
     */
    public function setPaidAmount(float $paidAmount): void
    {
        $this->paidAmount = $paidAmount;
    }

    /**
     * @return string
     */
    public function getPaidBy(): string
    {
        return $this->paidBy;
    }

    /**
     * @param string $paidBy
     */
    public function setPaidBy(string $paidBy): void
    {
        $this->paidBy = $paidBy;
    }

    /**
     * @return int
     */
    public function getBillingId(): int
    {
        return $this->billingId;
    }

    /**
     * @param int $billingId
     */
    public function setBillingId(int $billingId): void
    {
        $this->billingId = $billingId;
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
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
