<?php

namespace Users\Values;

use Exception;
use Laminas\Filter\FilterChain;

class TokenPost
{
    const EXCEPTION_USERNAME = 'Not sendend field username.';
    const EXCEPTION_PASSWORD = 'Not sendend field password.';
    const EXCEPTION_PASSWORD_LENGTH = 'Password with invalid length.';

    private array $data;
    private array $rawData;
    private string $appKey;

    /**
     * @throws Exception
     */
    public function __construct(array $data, $appKey)
    {
        $data['app_key'] = $appKey;

        $this->data = $data;
        $this->rawData = $data;

        $this->validate();
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        if (!$this->checkSetItem($this->data, 'username')) {
            throw new Exception(self::EXCEPTION_USERNAME);
        }

        if (!$this->checkSetItem($this->data, 'password')) {
            throw new Exception(self::EXCEPTION_PASSWORD);
        }

        if (!$this->checkPassword($this->data['password'])) {
            throw new Exception(self::EXCEPTION_PASSWORD_LENGTH);
        }

        $this->data['username'] = $this->filterString($this->data['username']);
        $this->data['password'] = $this->filterString($this->data['password']);

        return true;
    }

    /**
     * @param array $data
     * @param string $name
     * @return bool
     */
    private function checkSetItem(array $data, string $name): bool
    {
        return isset($data[$name]) && !empty($data[$name]);
    }

    /**
     * @param $password
     * @return bool
     */
    public function checkPassword($password): bool
    {
        $length = strlen($password);
        return ($length >= 6);
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

    public function getData(): array
    {
        return $this->data;
    }

    public function getRawData(): array
    {
        return $this->rawData;
    }
}
