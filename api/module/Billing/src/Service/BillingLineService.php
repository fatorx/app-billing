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

    private bool $stdOut = true;

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

            if ($this->stdOut) {
                printf($messageLog);
            }

            $e = new Exception(self::MESSAGE_EXCEPTION_DEBT_CHECK);
            $this->addLog($e);
            throw $e;
        }

        if ($invoice->getAmount() == 0) {
            $e = new Exception(self::MESSAGE_EXCEPTION_VALUE);
            $this->addLog($e);
            throw $e;
        }

        $this->em->persist($invoice);
        $this->em->flush();

        $dateTime = $this->getDateTime();
        $messageLog = $dateTime . " - Process invoice line: {$invoice->getDebtId()} - {$invoice->getEmail()}\n";
        $this->addLogMessage($messageLog, 'lines_billings');

        if ($this->stdOut) {
            printf($messageLog);
        }

        $mailMessage = new MailMessage($invoice);
        $this->producer->createMessage($mailMessage->getMessage(), ChannelsConfig::EMAILS);


        return true;
    }

    /**
     * @param bool $stdOut
     */
    public function setStdOut(bool $stdOut): void
    {
        $this->stdOut = $stdOut;
    }

    public function checkDebtId(Invoice $invoice): bool
    {
        $invoiceCheck = $this->em->getRepository($invoice::class)
                                 ->findOneBy(['debtId' => $invoice->getDebtId()]);

        return ($invoiceCheck == null);
    }
}
