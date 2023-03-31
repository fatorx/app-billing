<?php

namespace Billing\Service;

use Application\Service\BaseService;
use Billing\Message\Producer;
use Billing\Storage\StorageFile;
use Billing\Values\FileMessage;
use Billing\Values\PostFile;
use Exception;

/**
 * Class BillingService
 * @package Billing\Service
 */
class BillingService extends BaseService
{
    private StorageFile $storageFile;
    private Producer $producer;

    /**
     * StorageFile constructor.
     */
    public function __construct(StorageFile $storageFile, Producer $producer)
    {
        $this->storageFile = $storageFile;
        $this->producer = $producer;
    }

    /**
     * @throws Exception
     */
    public function storage(PostFile $postFile): string|null
    {
        $statusFile = $this->storageFile->persist($postFile);

        if ($statusFile) {
            $uuidStorage = $this->storageFile->getUuidStorage();
            $fileMessage = new FileMessage($uuidStorage);
            $this->producer->createMessage($fileMessage->getMessage());

            return $uuidStorage;
        }

        return null;
    }

}
