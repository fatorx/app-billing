<?php

namespace Users\Values;

use Exception;
use Laminas\Filter\FilterChain;
use Laminas\Validator\EmailAddress;
use Users\Exception\UserException;

class RecoverPost
{
    const EXCEPTION_EMAIL_EMPTY = 'Inform your email.';
    const EXCEPTION_EMAIL = 'Email not valid.';

    private array $data;
    private array $rawData;

    /**
     * @throws Exception
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->rawData = $data;

        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        if (!$this->checkSetItem($this->data)) {
            throw new Exception(self::EXCEPTION_EMAIL_EMPTY);
        }

        $validator = new EmailAddress();
        if (!$validator->isValid($this->data['email'])) {
            throw new UserException(self::EXCEPTION_EMAIL);
        }

        $this->data['email'] = $this->filterString($this->data['email']);

        return true;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function checkSetItem(array $data): bool
    {
        return isset($data['email']) && !empty($data['email']);
    }

    /**
     * @param string $name
     * @return string
     */
    private function filterString(string $name): string
    {
        $filter = new FilterChain();

        $filter->attachByName('StringTrim');
        $filter->attachByName('StripNewlines');
        $filter->attachByName('StripTags');

        return $filter->filter($name);
    }

    public function getEmail()
    {
        return $this->data['email'];
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }
}
