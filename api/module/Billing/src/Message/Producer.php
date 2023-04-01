<?php

namespace Billing\Message;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    const DEFAULT_CHANNEL = 'files';

    private AMQPStreamConnection $connection;

    /**
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $this->connection = new AMQPStreamConnection(
            $config['host'], $config['port'], $config['username'], $config['password']
        );
    }

    public function createMessage(string $message, string $channelName = self::DEFAULT_CHANNEL): true
    {
        $channel = $this->connection->channel();
        $channel->queue_declare(
            $channelName, false, false, false, false
        );

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', $channelName);

        return true;
    }
}
