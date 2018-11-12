<?php

use PHPUnit\Framework\TestCase;

use Mr\BootstrapTests\Mocks\MockClient;
use Mr\BootstrapTests\Mocks\MockUserRepository;
use Mr\BootstrapTests\Mocks\MockUser;

class BaseModelTest extends TestCase
{
    protected $userRepository;

    public function setUp()
    {
        $this->userRepository = new MockUserRepository(
            new MockClient(
                [
                    'responses' => []
                ]
            )
        );
    }

    public function testConstruct()
    {
        $data = [
            'username' => uniqid(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => uniqid() . '@test.com',
            'password' => '123',
            'password_confirmation' => '123'
        ];

        $model = new MockUser($this->userRepository, $data);

        $this->assertEquals($data, $model->toArray());
    }

    public function testDefaultId()
    {
        $data = [
            'id' => rand(1, 100),
            'name' => 'Test',
            'email' => uniqid() . '@test.com'
        ];

        $model = new MockUser($this->userRepository, $data);

        $this->assertEquals($data['id'], $model->id());
        $this->assertEquals($data['id'], $model->id);
    }

    public function testEmailAsIdField()
    {
        $data = [
            'name' => 'Test',
            'email' => uniqid() . '@test.com'
        ];

        $model = new MockUser($this->userRepository, $data);

        $model->useEmailAsIdField();

        $this->assertEquals($data['email'], $model->id());
        $this->assertEquals($data['email'], $model->id);
    }
}