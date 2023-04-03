<?php

namespace Billing\Values;

use Exception;
use Laminas\Stdlib\Parameters;

class PostFile
{
    const MESSAGE_INVALID_FILE = 'Arquivo não enviado.';
    const MESSAGE_INVALID_FORMAT = 'Formato de arquivo inválido (permitido somente no formato CSV).';
    const MESSAGE_INVALID_LENGTH = 'O arquivo enviado possui %2.f MB. O máximo permitido: %2.f MB.';

    const EXCEPTION_CODE_SIZE = 3002;

    const LIMIT_LENGTH = 1024000;

    protected Parameters $data;

    /**
     * @throws Exception
     */
    public function __construct(Parameters $data)
    {
        $this->data = $data;
        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): void
    {
        if (!isset($this->data['file'])) {
            throw new Exception(self::MESSAGE_INVALID_FILE);
        }

        $isEmptyFile = empty($this->data['file']['tmp_name']);
        if ($isEmptyFile) {
            throw new Exception(self::MESSAGE_INVALID_FILE);
        }

        $isValidType = ($this->data['file']['type'] === 'text/csv');
        if (!$isValidType) {
            throw new Exception(self::MESSAGE_INVALID_FORMAT);
        }

        $size = $this->data['file']['size'];
        $validLength = ($size <= self::LIMIT_LENGTH);
        if (!$validLength) {
            $size = $size / 1000000;
            $limit = self::LIMIT_LENGTH / 1000000;

            $message = sprintf(self::MESSAGE_INVALID_LENGTH, $size, $limit);
            throw new Exception($message, self::EXCEPTION_CODE_SIZE);
        }
    }

    public function getType(): string
    {
        return $this->data['file']['type'];
    }

    public function getTmpName(): string
    {
        return $this->data['file']['tmp_name'];
    }

    public function getData(): Parameters
    {
        return $this->data;
    }
}
