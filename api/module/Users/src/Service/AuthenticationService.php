<?php

namespace Users\Service;

use Application\Service\BaseService;

/**
 * Class AuthenticationService
 * @package Users\Service
 */
class AuthenticationService extends BaseService
{
    /**
     * @var bool
     */
    private bool $auth = false;

    public function hasIdentity(): bool
    {
        return $this->auth;
    }
}
