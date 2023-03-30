<?php

namespace ApplicationTest\Service;

use Application\Service\BaseService;

class BaseServiceTest extends BaseTestCase
{
    /**
     * @var BaseService
     */
    protected BaseService $baseService;

// --Commented out by Inspection START (3/10/23, 9:05 AM):
//    /**
//     * @var array
//     */
//    protected array $data = [];
// --Commented out by Inspection STOP (3/10/23, 9:05 AM)


    public static function setUpBeforeClass(): void
    {
        // actions
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->baseService = new BaseService();
        $this->baseService->setEm($this->em);
    }
}
