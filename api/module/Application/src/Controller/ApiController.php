<?php

namespace Application\Controller;

use Application\Jwt\Payload;
use Application\Service\BaseService;
use Datetime;
use Exception;
use Firebase\JWT\JWT;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\PhpEnvironment\Response;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\Stdlib\Parameters;
use Laminas\Stdlib\ResponseInterface;
use Laminas\View\Model\JsonModel;
use MongoDB\Driver\Exception\ExecutionTimeoutException;

class ApiController extends AbstractRestfulController implements IController
{

    /**
     * @var Integer $httpStatusCode Define Api Response code.
     */
    public int $httpStatusCode = 200;

    /**
     * @var array $apiResponse Define response for api
     */
    public array $apiResponse;

    /**
     *
     * @var string
     */
    public string $token;

    /**
     * @var mixed
     */
    protected mixed $tokenPayload = [];

    /**
     * set Event Manager to check Authorization
     * @param EventManagerInterface $events
     */
    public function setEventManager(EventManagerInterface $events)
    {
        parent::setEventManager($events);

        $events->attach('dispatch', [$this, 'checkAuthorization'], 10);
    }

    /**
     * This Function call from eventmanager to check authntication and token validation
     * @param $event
     * @return Response|ResponseInterface|void
     * @throws Exception
     */
    public function checkAuthorization($event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();

        $response = $event->getResponse();
        $routeMatch = $event->getRouteMatch();

        $isAuthorizationRequired = $routeMatch->getParam('isAuthorizationRequired');
        $methods = $routeMatch->getParam('methodsAuthorization');
        $serviceManager = $event->getApplication()->getServiceManager();
        $config = $serviceManager->get('Config');
        $event->setParam('config', $config);

        try {
            if (!empty($methods) && !in_array($method, $methods)) {
                return $this->stopRequest('HTTP Method ' . $method . ' not allowed for this action.');
            }

            if (isset($config['ApiRequest'])) {
                $configApi = $config['ApiRequest'];
                $responseFormat = $configApi['responseFormat'];
                $responseStatusKey = $responseFormat['statusKey'];
                if (!$isAuthorizationRequired) {
                    return;
                }
                //return $this->stopRequest('This action requires authentication.'); // @todo check access

                $jwtToken = $this->findJwtToken($request);

                if ($jwtToken) {
                    $this->token = $jwtToken;
                    $this->decodeJwtToken();
                    if (is_object($this->tokenPayload)) {
                        $this->preLoadMethod();
                        return; // $this->stopRequest('This token is not valid.');
                    }
                    $response->setStatusCode(400);
                    $jsonModelArr = [$responseStatusKey => $responseFormat['statusNokText'],
                        $responseFormat['resultKey'] => [$responseFormat['errorKey'] =>
                            $this->tokenPayload]];
                } else {
                    $response->setStatusCode(401);
                    $jsonModelArr = [$responseStatusKey => $responseFormat['statusNokText'],
                        $responseFormat['resultKey'] => [$responseFormat['errorKey'] =>
                            $responseFormat['authenticationRequireText']]];
                }
            } else {
                $response->setStatusCode(400);
                $jsonModelArr = [
                    'status' => 'NOK',
                    'result' => [
                        'error' => 'Require copy this file config\autoload\restapi.global.php'
                    ]
                ];
            }

        } catch (Exception $e) {
            $response->setStatusCode(400);
            $jsonModelArr = [
                'error' => $e->getMessage()
            ];
        }

        return $this->stopRequest($jsonModelArr);
    }

    /**
     * Check Request object have Authorization token or not
     * @param  $request
     * @return string|null String
     */
    public function findJwtToken($request): ?string
    {
        $jwtToken = $request->getHeaders("Authorization") ? $request->getHeaders("Authorization")->getFieldValue() : '';
        if ($jwtToken) {
            $token = str_replace('Bearer', '', $jwtToken);
            return trim($token);
        }

        return $jwtToken;
    }

    /**
     * contain user information for createing JWT Token
     * @param $payload
     * @return false|string
     */
    protected function generateJwtToken($payload): false|string
    {
        if (!is_array($payload) && !is_object($payload)) {
            $this->token = false;
            return false;
        }
        $this->tokenPayload = $payload;
        $config = $this->getEvent()->getParam('config', false);
        $jwtAuth = $config['ApiRequest']['jwtAuth'];
        $cypherKey = $jwtAuth['cypherKey'];
        $tokenAlgorithm = $jwtAuth['tokenAlgorithm'];
        $this->token = JWT::encode($this->tokenPayload, $cypherKey, $tokenAlgorithm);

        return $this->token;
    }

    /**
     * contain encoded token for user.
     */
    protected function decodeJwtToken()
    {
        if (!$this->token) {
            $this->tokenPayload = [];
        }

        $config = $this->getEvent()->getParam('config', false);
        $configJwtAuth = $config['ApiRequest']['jwtAuth'];

        $cypherKey = $configJwtAuth['cypherKey'];
        $tokenAlgorithm = $configJwtAuth['tokenAlgorithm'];
        try {
            $decodeToken = JWT::decode($this->token, $cypherKey, [$tokenAlgorithm]);
            $this->tokenPayload = $decodeToken;
        } catch (Exception $e) {
            $this->tokenPayload = $e->getMessage();
        }
    }

    /**
     * @return ?object
     */
    protected function getTokenPayload(): ?object
    {
        return $this->tokenPayload;
    }

    /**
     * @return Payload
     */
    protected function getPayload(): Payload
    {
        return new Payload($this->tokenPayload);
    }

    /**
     * Create Response for api Assign require data for response and
     * check is valid response or give error
     *
     * @param array $apiResponse
     * @return JsonModel
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function createResponse(array $apiResponse = []): JsonModel
    {
        $numArgs = count($apiResponse);

        $config = $this->getEvent()->getParam('config', false);
        $configResponseFormat = $config['ApiRequest']['responseFormat'];

        $event = $this->getEvent();
        $response = $event->getResponse();

        if ($numArgs > 0) {
            $response->setStatusCode($this->httpStatusCode);
        } else {
            $this->httpStatusCode = 500;
            $response->setStatusCode($this->httpStatusCode);
            $errorKey = $configResponseFormat['errorKey'];
            $defaultErrorText = $configResponseFormat['defaultErrorText'];
            $apiResponse[$errorKey] = $defaultErrorText;
        }

        $statusKey = $configResponseFormat['statusKey'];

        $sendResponse[$statusKey] = $configResponseFormat['statusNokText'];
        if ($this->httpStatusCode == 200) {
            $sendResponse[$statusKey] = $configResponseFormat['statusOkText'];
        }

        $sendResponse[$configResponseFormat['resultKey']] = $apiResponse;
        $sendResponse['request_time'] = $this->getDateTime();

        return new JsonModel($sendResponse);
    }

    public function stopRequest($message): Response|ResponseInterface
    {
        $data = [
            'message' => $message,
            'request_time' => (new Datetime())->format('Y-m-d H:i:s.u')
        ];

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $view = new JsonModel($data);
        $response->setContent($view->serialize());

        return $response;
    }

    public function setConfig(array $config)
    {
        // @todo: Implement setConfig() method.
    }

    /**
     * @param string|null $param Parameter name to retrieve, or null to get all.
     * @param mixed|null $default Default value to use when the parameter is missing.
     * @return mixed
     */
    public function getJsonParameters(string $param = null, mixed $default = null): mixed
    {
        /** @var Request $request */
        $request = $this->getRequest();
        $content = $request->getContent();
        $data = Json::decode($content, 1);
        if ($param == null) {
            return $data;
        }
        return $data[$param] ?? $default;
    }

    /**
     * @throws Exception
     */
    public function getFile(): Parameters
    {
        /** @var Request $request */
        $request = $this->getRequest();

        return $request->getFiles();
    }

    public function getDateTime(): string
    {
        return (new DateTime())->format("Y-m-d H:i:s.u");
    }

    /**
     * @throws Exception
     */
    public function preLoadMethod()
    {
        $expirationTime = $this->getPayload()->getExpiration();

        $dateTime = new Datetime();
        $compateTime = clone $dateTime;
        $compateTime->setTimestamp($expirationTime);

        $expiration = ($dateTime >= $compateTime);
        if ($expiration) {
            throw new Exception('Token expired.');
        }

        $userId = $this->getPayload()->getSub();

        $isInstance = ($this->service instanceof BaseService);
        if (!$isInstance) {
            throw new Exception('Service not configured.');
        }

        $this->service->setUserId($userId); // @todo review this point
    }
}
