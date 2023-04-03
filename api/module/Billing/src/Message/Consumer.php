<?php

namespace Billing\Message;

use DateTime;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer
{
    const DEFAULT_CHANNEL = 'test';

    private bool $startService = false;
    private AMQPStreamConnection $connection;

    private CallbackConsumer $callBackConsumer;

    /**
     * @throws Exception
     */
    public function __construct(ServiceManager $serviceManager, AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
        $this->callBackConsumer = new CallbackConsumer($serviceManager); // @todo review callback
    }

    public function waitingMessages(string $channelName = self::DEFAULT_CHANNEL, int $timeout = 0): void
    {
        $channel = $this->configureChannel($channelName);
        $this->displayMessage($channelName);

        $this->startService = true;
        while ($channel->is_open()) {
            if ($channelName == 'test') {
                $channel->close();
                break;
            }

            $channel->wait(null, $non_blocking = false, $timeout);
        }
    }

    public function getStartService(): bool
    {
        return $this->startService;
    }

    public function configureChannel(string $channelName): AMQPChannel
    {
        $channel = $this->connection->channel();

        $channel->queue_declare(
            $channelName, false, false, false, false
        );

        $channel->basic_consume(
            $channelName, '',
            false, false,
            false, false,
            $this->callBackConsumer);

        return $channel;
    }

    public function displayMessage($channelName): void
    {
        $dateTime = (new Datetime())->format('Y-m-d H:i:s');
        $messageLog = "{$dateTime} - waiting messages - {$channelName}\n";
        printf($messageLog);
    }

}
