<?php

namespace Billing\Service;

use Application\Service\BaseService;
use Billing\Entity\Invoice;
use Billing\Entity\Payment;
use Billing\Values\PaymentMessage;
use Exception;

/**
 * Class PaymentService
 * @package Billing\Service
 */
class PaymentService extends BaseService
{
    const MESSAGE_EXCEPTION_VALUE = 'Valor da fatura inválido.';
    const MESSAGE_EXCEPTION_PAYMENT_REGISTER = 'Pagamento já registrado.';
    const MESSAGE_EXCEPTION_BILLING_NOT_REGISTER = 'Cobrança não registrada.';

    /**
     * @throws Exception
     */
    public function process(PaymentMessage $paymentMessage): true
    {
        $payment = $paymentMessage->getPayment();
        if ($payment->getPaidAmount() == 0) {
            throw new Exception(self::MESSAGE_EXCEPTION_VALUE);
        }

        if (!$this->checkDebtId($payment)) {
            throw new Exception(self::MESSAGE_EXCEPTION_PAYMENT_REGISTER);
        }

        $invoice = $this->getInvoice($payment->getDebtId());
        $invoice->setStatus(1);

        $payment->setBillingId($invoice->getId());

        $this->em->persist($invoice);
        $this->em->persist($payment);
        $this->em->flush();

        return true;
    }

    public function checkDebtId(Payment $payment): bool
    {
        $repository = $this->em->getRepository(Payment::class);
        $debtCheck = $repository->findOneBy(
            ['debtId' => $payment->getDebtId()]
        );

        return ($debtCheck == null);
    }

    /**
     * @throws Exception
     */
    public function getInvoice(int $debtId): Invoice
    {
        $repository = $this->em->getRepository(Invoice::class);
        $invoice = $repository->findOneBy(
            ['debtId' => $debtId, 'status' => 0] // @todo log status
        );

        if ($invoice == null) {
            throw new Exception(self::MESSAGE_EXCEPTION_BILLING_NOT_REGISTER);
        }

        return $invoice;
    }
}
