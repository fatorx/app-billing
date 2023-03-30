<?php

namespace Users\Controller;

use Application\Controller\ApiController;
use Exception;
use Laminas\View\Model\JsonModel;
use Users\Consumer\RecoverPassword;
use Users\Service\AccountService;
use Users\Values\RecoverPost;

class AccountController extends ApiController
{
    /**
     * @var AccountService $service
     */
    protected AccountService $service;

    /**
     * @param AccountService $service
     */
    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    /**
     * @return JsonModel
     */
    public function indexAction(): JsonModel
    {
        try {
            $data = $this->service->getResumeData();
        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    /**
     * @return JsonModel
     */
    public function recoverPasswordAction(): JsonModel
    {
        try {
            $pars = $this->getJsonParameters();
            $recoverPost = new RecoverPost($pars);
            $this->service->startRecoverPassword($recoverPost);

            $data = [
                'message' => 'Start recover.',
                'data' =>  $recoverPost->getData(),
            ];

        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage(),
            ];
        }

        return $this->createResponse($data);
    }

    /**
     * @return JsonModel
     */
    public function logoutAction(): JsonModel
    {
        try {
            $this->service->clearAccessData();

            $data = [
                'message' => 'logout user.',
            ];

        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage(),
            ];
        }

        return $this->createResponse($data);
    }

    /**
     * @return void
     */
    public function recoverConsumerAction(): void
    {
        try {
            (new RecoverPassword($this->service));
        } catch (Exception $e) {
            printf($e->getMessage());
        }
    }

}
