<?php

namespace Billing\Message;

use DateTime;
use Exception;
use Laminas\ServiceManager\ServiceManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Consumer
{
    const DEFAULT_CHANNEL = 'files';

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

    public function waitingMessages(string $channelName = self::DEFAULT_CHANNEL): void
    {
        $channel = $this->connection->channel();

        $channel->queue_declare(
            $channelName, false, false, false, false
        );

        $dateTime = (new Datetime())->format('Y-m-d H:i:s');
        $messageLog = "{$dateTime} - waiting messages - {$channelName}\n";
        printf($messageLog);

        $channel->basic_consume(
            $channelName, '',
            false, false,
            false, false,
            $this->callBackConsumer);

        while ($channel->is_open()) {
            $channel->wait();
        }
    }


}
