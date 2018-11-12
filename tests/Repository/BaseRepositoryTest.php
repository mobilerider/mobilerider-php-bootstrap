<?php

use PHPUnit\Framework\TestCase;

use Mr\Bootstrap\Model\BaseModel;
use Mr\BootstrapTests\Mocks\MockUserRepository;
use Mr\BootstrapTests\Mocks\MockClient;
use Mr\Bootstrap\Container;
use Mr\BootstrapTests\Mocks\MockUser;
use Mr\Bootstrap\Repository\BaseRepository;

class BaseRepositoryTest extends TestCase
{
    protected $client;
    /**
     * Container
     *
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->client = new MockClient(
            [
                'responses' => []
            ]
        );

        $this->container = new Container(
            [
                MockUserRepository::class => [
                    'class' => MockUserRepository::class,
                    'arguments' => [
                        'client' => $this->client,
                        'options' => []
                    ]
                ],
                MockUser::class => [
                    'class' => MockUser::class,
                    'arguments' => [
                        'repository' => null,
                        'data' => []
                    ]
                ]
            ]
        );
    }

    public function testConstruct()
    {
        $repository = new MockUserRepository($this->client);

        $this->assertInstanceOf(BaseRepository::class, $repository);
    }

    public function testCreate()
    {
        $repository = $this->container->get(MockUserRepository::class);

        $model = $repository->create();

        $this->assertInstanceOf(BaseModel::class, $model);
    }

    public function testGet()
    {
        $data = [
            'meta_data' => [],
            'data' => [
                'id' => 123,
                'username' => uniqid(),
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => uniqid() . '@test.com'
            ]
        ];

        $this->client = new MockClient(
            [
                'responses' => [
                    [
                        'code' => '200',
                        'headers' => [],
                        'body' => json_encode($data)
                    ],
                    [
                        'code' => '200',
                        'headers' => [],
                        'body' => json_encode($data)
                    ]
                ]
            ]
        );

        $repository = $this->container->get(
            MockUserRepository::class, [
                'client' => $this->client
            ]
        );

        $result = $repository->get(123, [], true);

        $this->assertEquals($data['data'], $result);

        $result = $repository->get(123);

        $this->assertInstanceOf(BaseModel::class, $result);

        $this->assertEquals($data['data'], $result->toArray());
    }
}
