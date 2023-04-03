<?php

namespace Billing\Service;

use Application\Service\BaseService;
use Billing\Entity\Invoice;
use Billing\Message\ChannelsConfig;
use Billing\Message\Producer;
use Billing\Values\LineMessage;
use Billing\Values\MailMessage;
use Exception;

/**
 * Class BillingLineService
 * @package Billing\Service
 */
class BillingLineService extends BaseService
{
    const MESSAGE_EXCEPTION_DEBT_CHECK = 'Débito já registrado.';
    const MESSAGE_EXCEPTION_VALUE = 'Valor da fatura inválido.';

    private Producer $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @throws Exception
     */
    public function process(LineMessage $lineMessage): bool
    {
        $invoice = $lineMessage->getInvoice();

        if (!$this->checkDebtId($invoice)) {
            $dateTime = $this->getDateTime();
            $messageLog = $dateTime . " - No process invoice line: {$invoice->getDebtId()} - {$invoice->getEmail()}\n";
            printf($messageLog);

            throw new Exception(self::MESSAGE_EXCEPTION_DEBT_CHECK);
        }

        if ($invoice->getAmount() == 0) {
            throw new Exception(self::MESSAGE_EXCEPTION_VALUE);
        }

        $this->em->persist($invoice);
        $this->em->flush();

        $dateTime = $this->getDateTime();
        $messageLog = $dateTime . " - Process invoice line: {$invoice->getDebtId()} - {$invoice->getEmail()}\n";
        printf($messageLog);

        $mailMessage = new MailMessage($invoice);
        $this->producer->createMessage($mailMessage->getMessage(), ChannelsConfig::EMAILS);


        return true;
    }

    public function checkDebtId(Invoice $invoice): bool
    {
        $invoiceCheck = $this->em->getRepository($invoice::class)
                                 ->findOneBy(['debtId' => $invoice->getDebtId()]);

        return ($invoiceCheck == null);
    }
}
