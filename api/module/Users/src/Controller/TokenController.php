<?php

namespace Users\Controller;

use Application\Controller\ApiController;
use Exception;
use Laminas\Http\Request;
use Laminas\View\Model\JsonModel;
use Users\Service\TokenService;
use Users\Values\TokenPost;

class TokenController extends ApiController
{
    const MESSAGE_LOGGED = 'Logged in successfully.';

    /**
     * @var TokenService $service
     */
    protected TokenService $service;

    public function __construct(TokenService $service)
    {
        $this->service = $service;
    }

    public function indexAction(): JsonModel
    {
        try {
            $appKey = $this->getHeaderKey();
            $pars = $this->getJsonParameters();
            $tokenUser = new TokenPost($pars, $appKey);

            $user = $this->service->checkUser($tokenUser);
            $payload = $this->service->getPayloadData($user);

            $data = [
                'token' => $this->generateJwtToken($payload),
                'message' => self::MESSAGE_LOGGED
            ];

        } catch (Exception $e) {
            $this->httpStatusCode = 401;

            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    /**
     * @return string
     */
    private function getHeaderKey(): string
    {
        /** @var Request $request */
        $request = $this->getRequest();

        return $request->getHeaders()
            ->get('App-Key')->getFieldValue();
    }
}
