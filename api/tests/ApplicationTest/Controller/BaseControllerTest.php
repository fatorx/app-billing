<?php /** @noinspection ALL */
/** @noinspection ALL */
/** @noinspection ALL */

/** @noinspection ALL */

namespace ApplicationTest\Controller;

use Dotenv\Dotenv;
use Exception;
use Firebase\JWT\JWT;
use Laminas\Http\Headers;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Users\Entity\User;
use Users\Service\TokenService;

class BaseControllerTest extends AbstractHttpControllerTestCase
{
    const PATH_CACHE = __DIR__ . '/../../../data/cache';

    /**
     * @var array
     */
    public array $configApp = [];

    /**
     * @var array
     */
    public array $result = [];

    /**
     * @return void
     */
    public function setUp() : void
    {
        $path = __DIR__ . '/../../../';

        $dotenv = Dotenv::createImmutable($path, '.env');
        $dotenv->load();

        $configOverrides = include $path . 'config/autoload/local.php';

        $this->setApplicationConfig(ArrayUtils::merge(
            include $path . 'config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    /**
     * @return array
     */
    public function getConfigApp(): array
    {
        return $this->getApplicationConfig()['app'];
    }

    /**
     * @param array $config
     */
    public function setRequestHeadersParametersToken(array $config = [])
    {
        /** @var Request $request */
        $request = $this->getRequest();

        $headers = new Headers();
        $headers->addHeaderLine('App-Key', $config['app_key']);
        $headers->addHeaderLine('Content-Type', 'application/json');
        $request->setHeaders($headers);

        $postData = [
            'username' => $config['app_username'],
            'password' => $config['app_password'],
        ];
        $request->setMethod('POST')->setContent( Json::encode($postData)) ;
    }

    public function setTokenRequest()
    {
        $configApp = $this->getConfigApp();
        $this->setRequestHeadersParametersToken($configApp);
        $this->dispatch('/token');

        $content = $this->getResponse()->getContent();
        $result = Json::decode($content);

        $bearerToken = 'Bearer ' . $result->result->token;

        $headers = new Headers();
        $headers->addHeaderLine('Authorization', $bearerToken);
        $headers->addHeaderLine('Content-Type', 'application/json');

        /** @var Request $request */
        $request = $this->getRequest();
        $request->setHeaders($headers);
    }

    /**
     * @throws Exception
     */
    public function getRequestHeadersJwt(string $appendToken = '', bool $invalidToken = false): Request
    {
        $configApp = $this->getConfigApp();
        if ($invalidToken) {
            $token = $this->generateInvalidToken();
        } else {
            $token = $this->generateToken($configApp);
        }

        /** @var Request $request */
        $request = $this->getRequest();

        $headers = new Headers();
        $headers->addHeaderLine('App-Key', $configApp['app_key']);
        $headers->addHeaderLine('Authorization', 'Bearer ' . $token . $appendToken);
        $request->setHeaders($headers);

        return $request;
    }

    /**
     * @param array $postData
     * @return void
     */
    public function configurePostJson(array $postData)
    {
        $headers = new Headers();
        $headers->addHeaderLine('Content-Type', 'application/json');

        /** @var Request $request */
        $request = $this->getRequest();
        $request->setHeaders($headers);

        $postJson = Json::encode($postData);
        $request->setMethod('POST')->setContent( $postJson ) ;
    }

    /**
     * @throws Exception
     */
    public function generateToken(array $config = [], $regenerate = false)
    {
        $fileName = self::PATH_CACHE.'/token-app.txt';

        if (!is_file($fileName) || $regenerate) {
            $this->setRequestHeadersParametersToken($config);
            $this->dispatch('/token');

            $content = $this->getResponse()->getContent();
            $this->result = Json::decode($content, true);

            $isSetToken = isset($this->result['result']['token']);
            $this->assertTrue($isSetToken);
            if ($isSetToken) {
                file_put_contents($fileName, $this->result['result']['token']);
                return $this->result['result']['token'];
            }
        }

        return file_get_contents($fileName);
    }

    /**
     * @throws Exception
     */
    public function generateInvalidToken(array $config = [])
    {
        $serviceManager = $this->getApplication()->getServiceManager();

        /** @var TokenService $tokenService */
        $tokenService = $serviceManager->get(TokenService::class);

        $user = new User();
        $user->setId(100000);
        $user->setName('Million User');
        $payload = $tokenService->getPayloadData($user);

        $config = $this->getApplicationConfig();
        $cypherKey = $config['ApiRequest']['jwtAuth']['cypherKey'];
        $tokenAlgorithm = $config['ApiRequest']['jwtAuth']['tokenAlgorithm'];

        return JWT::encode($payload, $cypherKey, $tokenAlgorithm);
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    public function getResponseContent()
    {
        $content = $this->getResponse()->getContent();
        return Json::decode($content, true);
    }
}
