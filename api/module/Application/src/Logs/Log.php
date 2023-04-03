<?php

namespace Application\Logs;

use Datetime;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait Log
{
    public string $pathLogs = __DIR__ . '/../../../../data/logs/';

    /**
     * @param Exception $e
     * @param string $prefix
     * @param int $level
     */
    public function addLog(Exception $e, string $prefix = 'warning_' , int $level = Logger::WARNING): void
    {
        $date = new Datetime();

        // create a log channel
        $log = new Logger('App');
        $hourControl = $date->format('Y-m-d-H');
        $fileName = $prefix . $hourControl.'.txt';

        $log->pushHandler(new StreamHandler($this->pathLogs . $fileName, $level));

        $message = sprintf("%s - %s | %s", $e->getFile(), $e->getLine(), $e->getMessage());

        // add records to the log
        $headers = getallheaders();
        $clientIp = ($headers['X-Forwarded-For'] ?? 'NO IP');
        $log->error($clientIp . ' - ' . $message);
    }
}
