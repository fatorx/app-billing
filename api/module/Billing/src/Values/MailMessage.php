<?php

namespace Billing\Values;

use Billing\Entity\Invoice;
use Billing\Service\MailService;
use Exception;

class MailMessage
{
    const MESSAGE_EXCEPTION = 'E-mail inválido.';

    private Invoice $invoice;
    private string $type;

    /**
     * @throws Exception
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->type = MailService::class;

        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): true
    {
        if ($this->invoice->getEmail() === '') {
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
