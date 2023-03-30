<?php

namespace BillingTest\Controller;

use ApplicationTest\Controller\BaseControllerTest;
use Billing\Controller\BillingController;
use Exception;

/**
 * @group controllers
 * @group billing
 */
class BillingControllerTest extends BaseControllerTest
{
    /**
     * @throws Exception
     */
    public function testBillingHome()
    {
        $this->dispatch('/billing-home', 'GET');

        $this->assertResponseStatusCode(200);
        $this->assertModuleName('billing');
        $this->assertControllerName(BillingController::class);
        $this->assertControllerClass('BillingController');
        $this->assertMatchedRouteName('billing-index');
    }
}
