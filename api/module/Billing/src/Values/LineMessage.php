<?php

namespace Billing\Values;

use Billing\Entity\Invoice;
use Billing\Service\BillingLineService;
use Exception;

class LineMessage
{
    const MESSAGE_EXCEPTION = 'O nome do cliente não consta no arquivo.';

    private Invoice $invoice;
    private string $type;

    /**
     * @throws Exception
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->type = BillingLineService::class;

        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): true
    {
        if ($this->invoice->getName() === '') {
            // @todo add id
            throw new Exception(self::MESSAGE_EXCEPTION);
        }

        return true;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function getMessage(): string
    {
        return serialize($this);
    }
}
