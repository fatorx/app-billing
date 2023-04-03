<?php

namespace Billing\Service;

use Application\Service\BaseService;
use Billing\Entity\Invoice;
use Billing\Values\MailMessage;
use Exception;

/**
 * Class MailService
 * @package Billing\Service
 */
class MailService extends BaseService
{
    const MESSAGE_EXCEPTION_VALUE = 'Valor da fatura inválido.';

    /**
     * @throws Exception
     */
    public function process(MailMessage $mailMessage): true
    {
        $invoice = $mailMessage->getInvoice();
        if ($invoice->getAmount() == 0) {
            throw new Exception(self::MESSAGE_EXCEPTION_VALUE);
        }

        $message = $this->composeMessage($invoice);
        $this->sendMessage($invoice->getEmail(), $message);

        return true;
    }

    public function composeMessage(Invoice $invoice): string
    {
        $dueDate = $invoice->getDueDate();

        return <<<BODY
                    Olá {$invoice->getName()},
                    Consta em nossos registro a seguinte pendência:          
                    Valor: {$invoice->getAmount(true)}
                    Data de Vencimento: {$dueDate->format('d/m/Y')}
                    
                    Para mais informações sobre o débito, entre em contato com o número 0800-909091
                  BODY;
    }

    public function sendMessage(string $email, string $message)
    {
//        echo $this->getDateTime()."\n";
//        echo $email."\n";
//        echo $message."\n\n";

        // @todo implement provider
        // @todo add log email
    }
}
