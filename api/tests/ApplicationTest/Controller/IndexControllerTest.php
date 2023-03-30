<?php /** @noinspection PhpUnused */
/** @noinspection PhpUnused */
/** @noinspection PhpUnused */

/** @noinspection PhpUnused */

namespace ApplicationTest\Controller;

use Application\Controller\IndexController;
use Dotenv\Dotenv;
use Exception;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class IndexControllerTest extends AbstractHttpControllerTestCase
{
    public function setUp() : void
    {
        $configOverrides = [];
        $dotenv = Dotenv::createImmutable(getcwd());
        $dotenv->load();

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(IndexController::class);
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }

    /**
     * @throws Exception
     */
    public function testGetData()
    {
        $this->dispatch('/get-data', 'GET');
        $responseData = $this->getResponse()->getContent();
        $data = json_decode($responseData, true);

        $method = 'data';
        $this->assertEquals($method, $data['method']);
    }

    /**
     * @throws Exception
     */
    public function testInvalidRouteDoesNotCrash()
    {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
