<?php

namespace Billing\Message;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    private AMQPStreamConnection $connection;

    /**
     * @throws Exception
     */
    public function __construct(array $config)
    {
        $this->connection = new AMQPStreamConnection($config['host'], $config['port'], $config['username'], $config['password']);

    }

    public function createMessage(string $message): true
    {
        $channel = $this->connection->channel();
        $channel->queue_declare('hello', false, false, false, false);

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', 'hello');

        return true;
    }

}
