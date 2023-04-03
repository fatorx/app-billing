<?php

namespace BillingTest\Entity;

use Billing\Entity\Invoice;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 * @group entities
 */
class InvoiceTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $pars = $this->getPars();
        $invoice = new Invoice($pars);

        $this->assertEquals($pars['name'], $invoice->getName());
        $this->assertEquals($pars['government_id'], $invoice->getGovernmentId());
        $this->assertEquals($pars['email'], $invoice->getEmail());
        $this->assertEquals($pars['amount'], $invoice->getAmount());
    }

    /**
     * @throws Exception
     */
    public function testToArray()
    {
        $pars = $this->getPars();
        $invoice = new Invoice($pars);
        $dataArray =$invoice->toArray();

        $this->assertEquals($pars['name'], $dataArray['name']);
        $this->assertEquals($pars['government_id'], $dataArray['government_id']);
        $this->assertEquals($pars['email'], $dataArray['email']);
        $this->assertEquals($pars['amount'], $dataArray['amount']);
    }

    public function getPars(): array
    {
        return [
            'name' => 'Charles Dickens',
            'government_id' => '11111111111',
            'email' => 'charlesdickens@kanastra.com.br',
            'amount' => '10.00',
            'due_date' => '2023-03-30',
            'debt_id' => 6566
        ];
    }
}
