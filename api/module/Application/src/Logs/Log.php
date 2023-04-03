<?php

namespace Application\Logs;

use Datetime;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait Log
{
    public string $pathLogs = __DIR__ . '/../../../../data/logs/';

    /**
     * @param $message
     * @param string $prefix
     */
    public function addLog($message, string $prefix = 'error_'): void
    {
        $date = new Datetime();

        // create a log channel
        $log = new Logger('App');
        $hourControl = $date->format('Y-m-d-H');
        $fileName = $prefix . $hourControl.'.txt';

        $log->pushHandler(new StreamHandler($this->pathLogs . $fileName, Logger::WARNING));

        // add records to the log
        $headers = getallheaders();
        $clientIp = ($headers['X-Forwarded-For'] ?? 'NO IP');
        $log->error($clientIp . ' - ' . $message);
    }
}
