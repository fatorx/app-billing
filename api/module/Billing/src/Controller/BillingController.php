<?php

namespace Billing\Controller;

use Application\Controller\ApiController;
use Billing\Service\BillingService;
use Billing\Values\PostFile;
use Billing\Values\PostPayment;
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
                'storage_key' => $fileName,
            ];
        } catch (Exception $e) {
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
            $status = $this->service->consumerFiles();
            $data = [
                'status' => $status,
            ];
        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    public function consumerLinesAction(): JsonModel
    {
        try {
            $status = $this->service->consumerLines();
            $data = [
                'status' => $status,
            ];
        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    public function consumerEmailsAction(): JsonModel
    {
        try {
            $status = $this->service->consumerEmails();
            $data = [
                'status' => $status,
            ];
        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }

    public function consumerPaymentsAction(): JsonModel
    {
        try {
            $status = $this->service->consumerPayments();
            $data = [
                'status' => $status,
            ];
        } catch (Exception $e) {
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
            $postPayment = new PostPayment($data);
            $this->service->confirmPayment($postPayment);

            $data = [
                'receive_data' => $this->service->getStatus()
            ];
        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
        }

        return $this->createResponse($data);
    }
}
