<?php

namespace Billing\Values;

use Exception;

class FileMessage
{
    private string $uuid;

    /**
     * @throws Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): true
    {
        if ($this->uuid === null) {
            throw new Exception('Houve um problema ao enviar o arquivo.');
        }

        return true;
    }

    public function getMessage(): string
    {
        return serialize($this);
    }
}
