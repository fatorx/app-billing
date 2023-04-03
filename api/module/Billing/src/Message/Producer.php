<?php

namespace Billing\Message;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Producer
{
    const EXCEPTION_MESSAGE_EMPTY = 'Sem conteúdo para a mensagem.';

    const DEFAULT_CHANNEL = 'files';

    private AMQPStreamConnection $connection;

    /**
     * @throws Exception
     */
    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function createMessage(string $message, string $channelName = self::DEFAULT_CHANNEL): true
    {
        if ($message == '') {
            throw new Exception(self::EXCEPTION_MESSAGE_EMPTY);
        }

        $channel = $this->connection->channel();
        $channel->queue_declare(
            $channelName, false, false, false, false
        );

        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', $channelName);

        return true;
    }
}
