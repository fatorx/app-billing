<?php

namespace Billing\Service;

use Application\Service\BaseService;
use Billing\Entity\Invoice;
use Exception;

/**
 * Class MailService
 * @package Billing\Service
 */
class MailService extends BaseService
{
    /**
     * @throws Exception
     */
    public function sendMail(Invoice $invoice)
    {
        echo '<pre>'; var_dump($invoice); exit();
    }

}
