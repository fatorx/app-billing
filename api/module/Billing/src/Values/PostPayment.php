<?php

namespace Billing\Values;

use DateTime;
use Exception;
use Laminas\Validator\Date;
use Laminas\Validator\Digits;

class PostPayment
{
    const MESSAGE_EXCEPTION_REQUEST = 'A requisição está incompleta. Campos requiridos: %s.';
    const MESSAGE_EXCEPTION_FIELD_NULL = 'O campo %s não pode ser nulo.';
    const MESSAGE_EXCEPTION_DEBT_ID_INVALID = 'O campo debtId ser deve ser um inteiro.';
    const MESSAGE_EXCEPTION_DATE_INVALID = 'O campo paidAt deve ser enviado no formato 0000-00-00 00:00:00';
    const MESSAGE_EXCEPTION_PAID_AT_INVALID = 'O campo paidAt deve ser número. Ex: 123.56';

    const FIELDS = ['debtId', 'paidAt', 'paidAmount', 'paidBy'];

    private array $data;

    /**
     * @param array $data
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): void
    {
        $keys = array_keys($this->data);
        if ($keys !== self::FIELDS) {
            $fieldsRequired = implode(',', self::FIELDS);
            $message = sprintf(self::MESSAGE_EXCEPTION_REQUEST, $fieldsRequired);
            throw new Exception($message);
        }

        if ($this->data['debtId'] == '') {
            $message = sprintf(self::MESSAGE_EXCEPTION_FIELD_NULL, 'debitId');
            throw new Exception($message);
        }

        if (!is_numeric($this->data['debtId'])) {
            throw new Exception(self::MESSAGE_EXCEPTION_DEBT_ID_INVALID);
        }

        if ($this->data['paidAt'] == '') {
            $message = sprintf(self::MESSAGE_EXCEPTION_FIELD_NULL, 'paidAt');
            throw new Exception($message);
        }

        $this->validDate();

        $this->validAmount();

        if ($this->data['paidBy'] == '') {
            $message = sprintf(self::MESSAGE_EXCEPTION_FIELD_NULL, 'paidBy');
            throw new Exception($message);
        }

        $this->data['debtId'] = (int)$this->data['debtId'];
        $this->data['paidAt'] = new DateTime($this->data['paidAt']);
    }

    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function validDate(): void
    {
        $validDate = new Date();
        $validDate->setFormat('Y-m-d H:i:s');
        $isValidDate = $validDate->isValid($this->data['paidAt']);
        if (!$isValidDate) {
            throw new Exception(self::MESSAGE_EXCEPTION_DATE_INVALID);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function validAmount(): void
    {
        $validDigits = new Digits();
        $isValidAmount = $validDigits->isValid($this->data['paidAmount']);
        if (!$isValidAmount) {
            throw new Exception(self::MESSAGE_EXCEPTION_PAID_AT_INVALID);
        }
    }
}
