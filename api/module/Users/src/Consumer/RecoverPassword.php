<?php

namespace Users\Consumer;

use Exception;
use RedisException;
use Users\Service\AccountService;

class RecoverPassword
{
    public function __construct(AccountService $accountService)
    {
        $this->process($accountService);
    }

    /**
     * @param AccountService $accountService
     * @return void
     */
    public function process(AccountService $accountService): void
    {
        $execute = new ExecuteSearchEmail($accountService);
        $redis = $accountService->getStorage();

        try {
            /** @var string $channel */
            $channel = ['recover'];
            $redis->subscribe($channel, $execute);

        } catch (RedisException|Exception $e) {
            echo $e->getMessage() . "\n\n";
            $this->process($accountService);
        }
    }
}

