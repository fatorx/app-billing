<?php

namespace BillingTest\Entity;

use Billing\Entity\Payment;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 * @group entities
 */
class PaymentTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $pars = $this->getPars();
        $payment = new Payment($pars);

        $this->assertEquals($pars['debtId'], $payment->getDebtId());
        $this->assertEquals($pars['paidAt'], $payment->getPaidAt());
        $this->assertEquals($pars['paidAmount'], $payment->getPaidAmount());
        $this->assertEquals($pars['paidBy'], $payment->getPaidBy());
    }

    /**
     * @throws Exception
     */
    public function testStatus()
    {
        $pars = $this->getPars();
        $pars['status'] = 1;

        $payment = new Payment($pars);

        $this->assertEquals($pars['status'], $payment->getStatus());
    }

    /**
     * @throws Exception
     */
    public function testToArray()
    {
        $pars = $this->getPars();
        $payment = new Payment($pars);
        $dataArray = $payment->toArray();

        $this->assertEquals($pars['debtId'], $dataArray['debt_id']);
        $this->assertEquals($pars['paidAt'], $dataArray['paid_at']);
        $this->assertEquals($pars['paidAmount'], $dataArray['paid_amount']);
        $this->assertEquals($pars['paidBy'], $dataArray['paid_by']);
    }

    public function getPars(): array
    {
        return [
            'debtId' => 2535,
            'paidAt' => new DateTime('2023-04-03 10:00:00'),
            'paidAmount' => 125.50,
            'paidBy' => 'Jane Doe'
        ];
    }
}
