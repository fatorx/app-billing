<?php

namespace Billing\Values;

use Billing\Service\BillingFileService;
use Exception;

class FileMessage
{
    const MESSAGE_EXCEPTION = 'Houve um problema ao enviar o arquivo.';

    private string $uuid;
    private string $type;

    /**
     * @throws Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->type = BillingFileService::class;

        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): true
    {
        if ($this->uuid === '') {
            throw new Exception(self::MESSAGE_EXCEPTION);
        }

        return true;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getMessage(): string
    {
        return serialize($this);
    }
}
