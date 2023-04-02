<?php

namespace Billing\Message;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConnectionBroker
{
    static private ?ConnectionBroker $instance = null;

    private AMQPStreamConnection $connection;

    /**
     * @throws Exception
     */
    public function __construct(array $config)
    {
        try {
            $this->connection = new AMQPStreamConnection(
                $config['host'], $config['port'], $config['username'], $config['password']
            );
        } catch(Exception $e) {
            throw new Exception( 'Sistem não disponível.');

            // echo $e->getMessage(); // log message
            // exit();
        }
    }

    /**
     * @throws Exception
     */
    public static function getInstance(array $config): ?ConnectionBroker
    {
        if (!self::$instance) {
            self::$instance = new ConnectionBroker($config);
        }

        return self::$instance;
    }

    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }
}
