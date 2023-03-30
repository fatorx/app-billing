<?php

namespace Billing\Service;

use Application\Service\BaseService;

/**
 * Class BillingService
 * @package Billing\Service
 */
class BillingService extends BaseService
{
    /**
     * @var string
     */
    private string $entity;

    /**
     * TagService constructor.
     */
    public function __construct()
    {

    }

    public function getList(): array
    {
        return [];
    }
}
