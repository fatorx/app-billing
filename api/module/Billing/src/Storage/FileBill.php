<?php

namespace Billing\Storage;

use Application\Logs\Log;
use Billing\Entity\Invoice;

class FileBill
{
    use Log;

    private string $contentFile = '';

    public function __construct(Invoice $invoice)
    {
        $this->processFile($invoice);
    }

    private function processFile(Invoice $invoice): void
    {
        $this->contentFile = "{$invoice->getName()}-{$invoice->getEmail()}-{$invoice->getAmount()}";
        $this->addLogMessage($this->contentFile, 'emails_files_');
    }

    public function getContentFile(): string
    {
        return $this->contentFile;
    }
}
