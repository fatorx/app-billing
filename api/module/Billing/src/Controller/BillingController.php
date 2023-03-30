<?php

namespace Billing\Controller;

use Application\Controller\ApiController;
use Billing\Service\BillingService;
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

    /**
     * @return JsonModel
     */
    public function getList(): JsonModel
    {
        $data = [
            'users' => [],
            'method' => 'get'
        ];
        return $this->createResponse($data);
    }

    /**
     * @param $id
     * @return JsonModel
     */
    public function get($id): JsonModel
    {
        $data = [
            'id' => $id,
            'method' => 'get'
        ];
        return $this->createResponse($data);
    }

    /**
     * @param $data
     * @return JsonModel
     */
    public function create($data): JsonModel
    {
        try {
            $data = [
                'message' => 'create',
                'data' => $data,
                'id' => 0,
                'action' => 'create'
            ];

        } catch (Exception $e) {
            $this->httpStatusCode = 400;

            $data = [
                'message' => $e->getMessage(),
                'id' => 0,
                'action' => 'create'
            ];
        } finally {
            echo 'vai acontecer';
        }

        return $this->createResponse($data);
    }

    /**
     * @param $id
     * @param $data
     * @return JsonModel
     */
    public function update($id, $data): JsonModel
    {
        try {
            $data = [
                'item' => 'update',
                'data' => $data,
                'action' => 'update',
            ];

        } catch (Exception $e) {

            $this->httpStatusCode = 400;
            if ($e->getMessage() == 'Entity not found!') {
                $this->httpStatusCode = 404;
            }

            $data = [
                'message' => $e->getMessage(),
                'id' => 0,
                'action' => 'create'
            ];
        }

        return $this->createResponse($data);
    }

    public function postAction()
    {
        try {
            $data = $this->getJsonParameters();
            echo '<pre>'; var_dump($data); exit();
            $data = [];
        } catch(Exception $e) {
            $data = [];
        }

        return $this->createResponse($data);
    }

    /**
     * @param $id
     * @return JsonModel
     */
    public function delete($id): JsonModel
    {
        $data = [
            'item' => 'item',
            'action' => 'update'
        ];
        return $this->createResponse($data);
    }
}
