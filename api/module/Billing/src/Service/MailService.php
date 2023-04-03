<?php

namespace Billing\Service;

use Application\Logs\Log;
use Application\Service\BaseService;
use Billing\Entity\Invoice;
use Billing\Storage\FileBill;
use Billing\Values\MailMessage;
use Exception;

/**
 * Class MailService
 * @package Billing\Service
 */
class MailService extends BaseService
{
    use Log;

    const MESSAGE_EXCEPTION_VALUE = 'Valor da fatura inválido.';

    /**
     * @throws Exception
     */
    public function process(MailMessage $mailMessage): true
    {
        $invoice = $mailMessage->getInvoice();
        if ($invoice->getAmount() == 0) {
            $e = new Exception(self::MESSAGE_EXCEPTION_VALUE);
            $this->addLog($e);
            throw $e;
        }

        $message = $this->composeMessage($invoice);
        $fileContents = new FileBill($invoice);
        $this->sendMessage($invoice->getEmail(), $message, $fileContents->getContentFile());

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

    public function sendMessage(string $email, string $message, string $fileContents)
    {
        $blockMessage  = $this->getDateTime() . "\n";
        $blockMessage .= $email."\n";
        $blockMessage .= $message."\n\n";

        // @todo $fileContents contains file in base_64

        // @todo implement provider

        $this->addLogMessage($blockMessage, 'emails_sended_');
    }
}
