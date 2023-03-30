<?php

namespace Users\Values;

use Laminas\Filter\FilterChain;
use Laminas\Validator\EmailAddress;
use Users\Exception\UserException;

class UserPost
{
    const EXCEPTION_NAME = 'Item name not sent!';
    const EXCEPTION_USERNAME = 'Item user_name not sent!';
    const EXCEPTION_NAME_LENGTH = 'Name lenght is invalid!';
    const EXCEPTION_EMAIL_LENGTH = 'Email lenght is invalid!';
    const EXCEPTION_EMAIL = 'Email is not valid!';

    private array $data;
    private array $dataRaw;

    /**
     * @throws UserException
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->dataRaw = $data;

        $this->validate();
    }

    /**
     * @throws UserException
     */
    public function validate(): bool
    {
        if (!$this->checkSetItem($this->data, 'name')) {
            throw new UserException(self::EXCEPTION_NAME);
        }

        if (!$this->checkSetItem($this->data, 'user_name')) {
            throw new UserException(self::EXCEPTION_USERNAME);
        }

        if (isset($this->data['name']) && strlen($this->data['name']) < 10) {
            throw new UserException(self::EXCEPTION_NAME_LENGTH);
        }

        if (isset($this->data['email']) && strlen($this->data['email']) < 10) {
            throw new UserException(self::EXCEPTION_EMAIL_LENGTH);
        }

        $validator = new EmailAddress();
        if (!$validator->isValid($this->data['email'])) {
            throw new UserException(self::EXCEPTION_EMAIL);
        }

        $this->data['name'] = $this->filterName($this->data['name']);
        $this->data['user_name'] = $this->filterName($this->data['user_name']);

        return true;
    }

    /**
     * @param array $data
     * @param string $name
     * @return bool
     */
    private function checkSetItem(array $data, string $name): bool
    {
        return isset($data[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    private function filterName(string $name): string
    {
        $filter = new FilterChain();

        $filter->attachByName('StringTrim');
        $filter->attachByName('StripNewlines');
        $filter->attachByName('StripTags');

        return $filter->filter($name);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->dataRaw;
    }

    /**
     * @return array
     */
    public function getDataExpose(): array
    {
        $data = $this->data;
        unset($data['password']);

        return $this->data;
    }
}
