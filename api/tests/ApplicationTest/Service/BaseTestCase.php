<?php /** @noinspection ALL */

namespace ApplicationTest\Service;

use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityRepository;
use Dotenv\Dotenv;
use Exception;
use Laminas\Stdlib\ArrayUtils;
use Mockery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;

class BaseTestCase extends TestCase
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

    protected mixed $findOneByResult;

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

        $this->em = $this->getEmMock();

        parent::setUp();
    }

    /**
     * @param array $data
     * @return EntityManager
     */
    public function getEmMock(array $data = []): EntityManager
    {
        $connect = $this->createMock(Connection::class);

        $countData = count($data);
        if ($countData == 0) {
            $connect->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($this->getStatementMock()));
        } else {
            $statement = $this->getStatementMockData($data);

            $connect->expects($this->any())
                ->method('prepare')
                ->will($this->returnValue($statement));
        }

        $connect->expects($this->any())
            ->method('lastInsertId')
            ->will($this->returnValue(1));

        $emMock = $this->createMock(EntityManager::class);
        $emMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connect));

        $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->getEntityRepositoryMock()));

        return $emMock;
    }

    /**
     * @param array $data
     * @return EntityManager
     */
    public function getEmMockData(array $data = []): EntityManager
    {
        $connect   = Mockery::mock(Connection::class);

        $connect->shouldReceive('execute')
            ->andReturn(null);

        foreach($data['fetch'] as $fetch)  {

            $statement = Mockery::mock(Statement::class);
            $statement->shouldReceive('fetch')
                ->andReturn($fetch['return']);

            $connect->shouldReceive('prepare')
                ->with($fetch['arg'])
                ->andReturn($statement);
        }

        $connect->shouldReceive('insert')
            ->andReturn(null);

        $connect->shouldReceive('update')
            ->andReturn(null);

        $connect->shouldReceive('lastInsertId')
            ->andReturn(1);

        $emMock = Mockery::mock(EntityManager::class);
        $emMock->shouldReceive('getConnection')
            ->andReturn($connect);

        return $emMock;
    }

    /**
     * @return Statement
     */
    protected function getStatementMock(): Statement
    {
        $result = $this->createMock(Result::class);

        $result->expects($this->any())
            ->method('fetchAssociative')
            ->will($this->returnValue($this->returnFetch));

        $result->expects($this->any())
            ->method('fetchAllAssociative')
            ->will($this->returnValue($this->returnFetch));

        $statement = $this->createMock(Statement::class);

        $statement->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnValue($result));

        return $statement;
    }

    /**
     * @param $returnData
     * @return Statement
     */
    public function getStatementMockData($returnData): Statement
    {
        $statement = Mockery::mock(Statement::class);

        $statement->shouldReceive('execute')
            ->andReturn(null);

        // SELECT id FROM ftm_transactions where order_payment = ?
        // null

        $statement->shouldReceive('fetch')
            ->with('SELECT id FROM ftm_transactions where order_payment = ?')
            ->andReturn(null);

        /*
        foreach ($returnData['fetch'] as $fetch) {
            echo $fetch['arg']."\n";
            $statement->shouldReceive('fetch')
                      ->with($fetch['arg'])
                      ->andReturn($fetch['return']);
        }
        */


        $statement->shouldReceive('fetch')
            ->with(1)
            ->andReturn('model');

        foreach ($returnData['fetchAll'] as $fetch) {
            $statement->shouldReceive('fetchAll')
                ->with($fetch['arg'])
                ->andReturn($fetch['return']);
        }

        return $statement;
    }

    /**
     * @return EntityManager
     */
    public function getEmMockException(): EntityManager
    {
        $connect = $this->createMock(Connection::class);
        $connect->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($this->getStatementMockException()));

        $connect->expects($this->any())
            ->method('insert')
            ->willThrowException(new Exception("Erro de sistema"));

        $connect->expects($this->any())
            ->method('update')
            ->willThrowException(new Exception("Erro de sistema"));

        $emMock = $this->createMock(EntityManager::class);
        $emMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connect));

        return $emMock;
    }

    protected function getStatementMockException(): Statement|MockObject
    {
        $statement = $this->createMock(Statement::class);

        $statement->expects($this->any())
            ->method('executeQuery')
            ->willThrowException(new Exception("Erro de sistema"));

        return $statement;
    }

    public function setFindOneByResult($findOneByResult)
    {
        $this->findOneByResult = $findOneByResult;
    }

    public function getEntityRepositoryMock(): EntityRepository|MockObject
    {
        $repository = $this->createMock(EntityRepository::class);
        $returnValue = $this->returnValue(1);

        $repository->expects($this->any())
            ->method('findOneBy')
            ->will($returnValue);

        return $repository;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}
