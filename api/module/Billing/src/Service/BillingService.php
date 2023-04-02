<?php

namespace Billing\Service;

use Application\Service\BaseService;
use Billing\Entity\Payment;
use Billing\Message\ChannelsConfig;
use Billing\Message\Consumer;
use Billing\Message\Producer;
use Billing\Storage\StorageFile;
use Billing\Values\FileMessage;
use Billing\Values\PaymentMessage;
use Billing\Values\PostFile;
use Billing\Values\PostPayment;
use Exception;

/**
 * Class BillingService
 * @package Billing\Service
 */
class BillingService extends BaseService
{
    private StorageFile $storageFile;
    private Producer $producer;
    private Consumer $consumer;

    /**
     * StorageFile constructor.
     */
    public function __construct(StorageFile $storageFile, Producer $producer, Consumer $consumer)
    {
        $this->storageFile = $storageFile;
        $this->producer = $producer;
        $this->consumer = $consumer;
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
            $this->producer->createMessage(
                $fileMessage->getMessage(), ChannelsConfig::FILES
            );

            return $uuidStorage;
        }

        return null;
    }

    /**
     * @throws Exception
     */
    public function confirmPayment(PostPayment $postPayment): true
    {
        $payment = new Payment($postPayment->getData());

        $fileMessage = new PaymentMessage($payment);
        $this->status = $this->producer->createMessage(
            $fileMessage->getMessage(), ChannelsConfig::PAYMENTS
        );

        return true;
    }

    public function consumerFiles(): true
    {
        $this->consumer->waitingMessages(ChannelsConfig::FILES);
        return true;
    }

    public function consumerLines(): true
    {
        $this->consumer->waitingMessages(ChannelsConfig::LINES);
        return true;
    }

    public function consumerEmails(): true
    {
        $this->consumer->waitingMessages(ChannelsConfig::EMAILS);
        return true;
    }

    public function consumerPayments(): true
    {
        $this->consumer->waitingMessages(ChannelsConfig::PAYMENTS);
        return true;
    }
}

