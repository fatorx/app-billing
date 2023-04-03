<?php

namespace BillingTest\Values;

use Billing\Values\PostPayment;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group billing
 */
class PostPaymentTest extends TestCase
{
    private array $data;

    public function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'debtId' => 123,
            'paidAt' => '2022-06-09 10:00:00',
            'paidAmount' => 1001.10,
            'paidBy' => 'John Doe'
        ];
    }

    /**
     * @throws Exception
     */
    public function testConstructor()
    {
        $postPayment = new PostPayment($this->data);
        $data = $postPayment->getData();

        $this->assertInstanceOf(PostPayment::class, $postPayment);
        $this->assertEquals($data['paidBy'], $this->data['paidBy']);
    }

    /**
     * @throws Exception
     */
    public function testRequestException()
    {
        $this->expectExceptionCode(PostPayment::EXCEPTION_CODE_REQUEST);

        unset($this->data['paidBy']);

        (new PostPayment($this->data));
    }

    /**
     * @throws Exception
     */
    public function testDebitIdNullException()
    {
        $this->expectExceptionCode(PostPayment::EXCEPTION_CODE_FIELD_NULL);

        $this->data['debtId'] = '';
        (new PostPayment($this->data));
    }

    /**
     * @throws Exception
     */
    public function testDebitIdNotNumericException()
    {
        $this->expectExceptionCode(PostPayment::EXCEPTION_CODE_DEBT_ID_INVALID);

        $this->data['debtId'] = '123a';
        (new PostPayment($this->data));
    }

    /**
     * @throws Exception
     */
    public function testPaidAtNullException()
    {
        $this->expectExceptionCode(PostPayment::EXCEPTION_CODE_FIELD_NULL);

        $this->data['paidAt'] = '';
        (new PostPayment($this->data));
    }

    /**
     * @throws Exception
     */
    public function testPaidAtException()
    {
        $this->expectExceptionCode(PostPayment::EXCEPTION_CODE_DATE_INVALID);

        $this->data['paidAt'] = '2023-03-35 10:00:00';
        (new PostPayment($this->data));
    }

    /**
     * @throws Exception
     */
    public function testValidAmountException()
    {
        $this->expectExceptionCode(PostPayment::EXCEPTION_CODE_PAID_AMOUNT_INVALID);

        $this->data['paidAmount'] = '1000a';
        (new PostPayment($this->data));
    }

    /**
     * @throws Exception
     */
    public function testPaidByNullException()
    {
        $this->expectExceptionCode(PostPayment::EXCEPTION_CODE_FIELD_NULL);

        $this->data['paidBy'] = '';
        (new PostPayment($this->data));
    }
}
