<?php

namespace Billing\Service;

use Application\Service\BaseService;
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

        // @todo add valid enter
        $this->em->persist($invoice);
        $this->em->flush();

        $dateTime = $this->getDateTime();
        $messageLog = $dateTime . " - Process invoice line: {$invoice->getId()} - {$invoice->getEmail()}\n";
        printf($messageLog);

        $mailMessage = new MailMessage($invoice);
        $this->producer->createMessage($mailMessage->getMessage());
    }
}
