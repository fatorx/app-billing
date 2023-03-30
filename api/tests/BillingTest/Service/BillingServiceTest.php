<?php

namespace BillingTest\Service;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Service\BillingService;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @group user
 */
class BillingServiceTest extends TestCase
{
    use ApplicationTestTrait;

    /**
     * @var BillingService
     */
    protected BillingService $service;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();

        /** @var  BillingService $this- >service */
        $this->service = $this->getApplicationServiceLocator()->get(BillingService::class);
    }

    /**
     * @group database
     */
    public function testGetList()
    {
        $users = $this->service->getList();

        $this->assertIsArray($users);
    }
}
