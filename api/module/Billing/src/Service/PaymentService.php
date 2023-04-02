<?php

namespace Billing\Service;

use Application\Service\BaseService;
use Billing\Values\PaymentMessage;
use Exception;

/**
 * Class PaymentService
 * @package Billing\Service
 */
class PaymentService extends BaseService
{
    /**
     * @throws Exception
     */
    public function process(PaymentMessage $paymentMessage)
    {
        $payment = $paymentMessage->getPayment();

        // @todo valid enter data

        $this->em->persist($payment);
        $this->em->flush();

        echo '<pre>'; var_dump($payment->toArray()); exit();
    }
}
