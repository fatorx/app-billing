<?php

namespace BillingTest\Controller;

use ApplicationTest\Controller\BaseControllerTest;
use Exception;
use Billing\Controller\BillingController;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Laminas\Stdlib\Parameters;

/**
 * @group controllers
 * @group billing
 */
class BillingControllerTest extends BaseControllerTest
{
    private string $module;
    private string $controllerName;
    private string $controllerClass;

    private string $version;

    public function setUp(): void
    {
        parent::setUp();

        $this->version = '/v1';

        $this->module = 'billing';
        $this->controllerName = BillingController::class;
        $this->controllerClass = 'BillingController';
    }

    /**
     * @throws Exception
     */
    public function testSendFile()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $content = file_get_contents('/tmp/test.csv');
        $length = strlen($content);

        $upload = new Parameters([
            'file' => [
                'name' => 'file.csv',
                'type' => 'text/csv',
                'tmp_name' => '/tmp/test.csv',
                'error' => 0,
                'size' => $length
            ]
        ]);
        $request->setFiles($upload);

        $route = $this->version . '/billing/send-file';

        $this->dispatch($route, 'POST');
        $this->moduleTest(200, 'billing-send-file');
    }

    /**
     * @throws Exception
     */
    public function testSendFileException()
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $upload = new Parameters([
            'file' => [
                'name' => 'blah.blah',
                'type' => 'blah',
                'tmp_name' => '/tmp/blah',
                'error' => 0
            ]
        ]);
        $request->setFiles($upload);

        $route = $this->version . '/billing/send-file';

        $this->dispatch($route, 'POST');
        $this->moduleTest(400, 'billing-send-file');
    }

    public function testWebHook()
    {
        $route = $this->version . '/billing/send-file';

        $this->dispatch($route, 'POST');
        $this->moduleTest(400, 'billing-webhook');
    }
    // /v1/billing/webhook


    private function moduleTest(int $code, string $route)
    {
        $this->assertResponseStatusCode($code);

        $this->assertModuleName($this->module);
        $this->assertControllerName($this->controllerName);
        $this->assertControllerClass($this->controllerClass);

        $this->assertMatchedRouteName($route);
    }
}
