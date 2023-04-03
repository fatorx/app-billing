<?php

namespace BillingTest\Message;

use ApplicationTest\Util\ApplicationTestTrait;
use Billing\Message\Producer;
use PHPUnit\Framework\TestCase;

class ProducerTest extends TestCase
{
    use ApplicationTestTrait;

    private Producer $producer;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Producer $producer */
        $this->producer = $this->getApplicationServiceLocator()->get(Producer::class);
    }

    public function testNot()
    {
        $this->fail('ProducerTest start');
    }
}
