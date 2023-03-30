<?php

namespace Users\Consumer;

use Exception;
use Laminas\Json\Json;
use Users\Service\AccountService;

class ExecuteSearchEmail
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * @throws Exception
     */
    public function __invoke($redis, $channel, $message): void
    {
        echo (new \DateTime())->format('H:i:s') . "\n\n";

        $pars = Json::decode($message, true);
        $email = $pars['email'];

        for ($i = 0; $i < 100; ++$i) {
            $data = $this->accountService->seachUserEmail($email);
            // @todo add send e-mail here

            echo $i." - ";
            var_dump($data);
            echo "\n";
        }
    }
}
