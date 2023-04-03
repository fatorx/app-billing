<?php

namespace Billing\Controller;

use Application\Controller\ApiController;
use Application\Logs\Log;
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
    use Log;

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
            $storageKey = $this->service->storage($postFile);
            $data = [
                'storage_key' => $storageKey,
            ];
        } catch (Exception $e) {
            $this->httpStatusCode = 400;
            $data = [
                'message' => $e->getMessage()
            ];
            $this->addLog($e, 'send_file_');
        }

        return $this->createResponse($data);
    }

    public function consumerFilesAction()
    {
        try {
            $status = $this->service->consumerFiles();
        } catch (Exception $e) {
            $this->addLog($e);
        }
    }

    public function consumerLinesAction()
    {
        try {
            $status = $this->service->consumerLines();
        } catch (Exception $e) {
            $this->addLog($e);
        }
    }

    public function consumerEmailsAction()
    {
        try {
            $status = $this->service->consumerEmails();
        } catch (Exception $e) {
            $this->addLog($e);
        }
    }

    public function consumerPaymentsAction()
    {
        try {
            $status = $this->service->consumerPayments();
        } catch (Exception $e) {
            $this->addLog($e);
        }
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
            $this->addLog($e, 'webhoook_');
        }

        return $this->createResponse($data);
    }
}
