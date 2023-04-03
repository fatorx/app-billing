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
    private Producer $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @throws Exception
     */
    public function process(LineMessage $lineMessage)
    {
        $invoice = $lineMessage->getInvoice();

        if ($this->checkDebtId($invoice)) {

            $this->em->persist($invoice);
            $this->em->flush();

            $dateTime = $this->getDateTime();
            $messageLog = $dateTime . " - Process invoice line: {$invoice->getDebtId()} - {$invoice->getEmail()}\n";
            printf($messageLog);

            $mailMessage = new MailMessage($invoice);
            $this->producer->createMessage($mailMessage->getMessage(), ChannelsConfig::EMAILS);
        } else {
            $dateTime = $this->getDateTime();
            $messageLog = $dateTime . " - No process invoice line: {$invoice->getDebtId()} - {$invoice->getEmail()}\n";
            printf($messageLog);
        }
    }

    public function checkDebtId(Invoice $invoice): bool
    {
        $invoiceCheck = $this->em->getRepository($invoice::class)
                                 ->findOneBy(['debtId' => $invoice->getDebtId()]);

        return ($invoiceCheck == null);
    }
}
