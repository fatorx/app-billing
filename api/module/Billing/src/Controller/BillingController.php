<?php

namespace Billing\Controller;

use Application\Controller\ApiController;
use Billing\Service\BillingService;
use Billing\Values\PostFile;
use Exception;
use Laminas\View\Model\JsonModel;

/**
 * Class BillingController
 * @package Billing\Controller
 */
class BillingController extends ApiController
{
    /**
     * @var BillingService $service
     */
    protected BillingService $service;

    /**
     * @param BillingService $service
     */
    public function __construct(BillingService $service)
    {
        $this->service = $service;
    }

    public function sendFileAction(): JsonModel
    {
        try {
            $data = $this->getFile();
            $postFile = new PostFile($data);
            $fileName = $this->service->storage($postFile);
            $data = [
                'send_file' => $fileName,
            ];
        } catch(Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    public function consumerFilesAction(): JsonModel
    {
        try {
            $fileName = $this->service->consumerFiles();
            $data = [
                'send_file' => $fileName,
            ];
        } catch(Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    public function webhookAction(): JsonModel
    {
        try {
            $data = $this->getJsonParameters();

            $str = '';
            $limit = 10000000;
            for ($i = 0; $i < $limit; ++$i) {
                $str .= $i . '-';
            }

            $data = [
                'data' => $data
            ];
        } catch(Exception $e) {
            $data = [];
        }

        return $this->createResponse($data);
    }
}
