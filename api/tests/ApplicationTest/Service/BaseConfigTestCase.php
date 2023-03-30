<?php /** @noinspection ALL */
/** @noinspection ALL */

/** @noinspection ALL */

namespace ApplicationTest\Service;

use Doctrine\ORM\EntityManager;
use Dotenv\Dotenv;
use Laminas\Stdlib\ArrayUtils;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Redis;
use RedisException;

class BaseConfigTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var EntityManager $em
     */
    protected EntityManager $em;

    /**
     * @var array
     */
    protected array $config = [];

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var array
     */
    protected array $returnFetch = [];


    public function setUp(): void
    {
        // actions
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../', '.env');
        $dotenv->load();

        $configOverrides = include __DIR__ . '/../../../config/autoload/local.php';
        $configLocal = include __DIR__ . '/../../../config/autoload/test.data.php';
        $this->config = ArrayUtils::merge(
            $configLocal['app'],
            $configOverrides['app']
        );

        parent::setUp();
    }


    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @throws RedisException
     */
    public function getStorage(): Redis
    {
        $redis = new Redis();
        $redis->connect($this->config['redis_host']);
        return $redis;
    }
}
