<?php

namespace Billing\Service;

use Application\Logs\Log;
use Application\Service\BaseService;
use Billing\Entity\Invoice;
use Billing\Message\ChannelsConfig;
use Billing\Message\Producer;
use Billing\Storage\StorageFile;
use Billing\Values\FileMessage;
use Billing\Values\LineMessage;
use Exception;
use MongoDB\Driver\Exception\ExecutionTimeoutException;

/**
 * Class BillingFileService
 * @package Billing\Service
 */
class BillingFileService extends BaseService
{
    use Log;

    const INDEX_NAME = 0;
    const INDEX_GOVERNMENT = 1;
    const INDEX_EMAIL = 2;
    const INDEX_AMOUNT = 3;
    const INDEX_DUE_DATE = 4;
    const INDEX_DEBT = 5;

    private StorageFile $storageFile;
    private Producer $producer;

    /**
     * BillingFileService constructor.
     */
    public function __construct(StorageFile $storageFile, Producer $producer)
    {
        $this->storageFile = $storageFile;
        $this->producer = $producer;
    }

    /**
     * @throws Exception
     */
    public function process(FileMessage $fileMessage): bool
    {
        $uuid = $fileMessage->getUuid();
        $lines = $this->storageFile->get($uuid);
        array_shift($lines);

        foreach ($lines as $numberLine => $line) {
            if ($line[self::INDEX_NAME] == null) {
                $e = new Exception("Linha {$numberLine} do arquivo {$uuid} nula.");
                $this->addLog($e);
                continue;
            }

            $isValidLine = $this->validateLine($line);
            $hydrateInvoice = $this->getDataLine($line);

            if (!$isValidLine) {
                $e = new Exception("Linha {$numberLine} do arquivo {$uuid} inválida.");
                $this->addLog($e);
                continue;
            }

            $invoice = new Invoice($hydrateInvoice);

            $fileMessage = new LineMessage($invoice);
            $this->producer->createMessage($fileMessage->getMessage(), ChannelsConfig::LINES);

            $dateTime = $this->getDateTime();
            $messageLog = $dateTime . " - Process invoice file line: {$invoice->getDebtId()} - {$invoice->getEmail()}\n";
            printf($messageLog);
        }

        return true;
    }

    public function validateLine(array $line): bool
    {
        return !(empty($line[self::INDEX_NAME]) || empty($line[self::INDEX_EMAIL]) ||
            empty($line[self::INDEX_AMOUNT]) || empty($line[self::INDEX_DUE_DATE]) ||
            empty($line[self::INDEX_DEBT]));
    }

    /**
     * @param mixed $line
     * @return array
     */
    private function getDataLine(mixed $line): array
    {
        return [
            'name' => $line[self::INDEX_NAME],
            'government_id' => $line[self::INDEX_GOVERNMENT],
            'email' => $line[self::INDEX_EMAIL],
            'amount' => $line[self::INDEX_AMOUNT],
            'due_date' => $line[self::INDEX_DUE_DATE],
            'debt_id' => (int)$line[self::INDEX_DEBT],
        ];
    }
}
