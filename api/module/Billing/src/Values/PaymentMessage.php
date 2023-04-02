<?php

namespace Billing\Values;

use Billing\Entity\Payment;
use Billing\Service\PaymentService;
use Exception;

class PaymentMessage
{
    const MESSAGE_EXCEPTION = 'Valor zerado no envio.';

    private Payment $payment;
    private string $type;

    /**
     * @throws Exception
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->type = PaymentService::class;

        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): true
    {
        if ($this->payment->getPaidAmount() == 0) {
            throw new Exception(self::MESSAGE_EXCEPTION);
        }

        return true;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getMessage(): string
    {
        return serialize($this);
    }
}
