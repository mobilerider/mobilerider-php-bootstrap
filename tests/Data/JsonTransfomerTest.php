<?php

namespace Mr\BootstrapTest\Traits;

use Mr\Bootstrap\Data\JsonEncoder;
use PHPUnit\Framework\TestCase;


class JsonTransfomerTest extends TestCase
{
    /**
     * @var JsonEncoder
     */
    protected $instance;

    protected $data = [
        'name' => 'This is a name',
        'settings' => [
            'destination' => [
                'uri' => 'http://localhost',
                'auth' => [
                    'type' => 'basic'
                ]
            ]
        ]
    ];

    public function setUp()
    {
        $this->instance = new JsonEncoder();
    }

    public function testEncode()
    {
        // Do not escape slashes by default
        $expected = '{"name":"This is a name","settings":{"destination":{"uri":"http://localhost","auth":{"type":"basic"}}}}';

        $json = $this->instance->encode($this->data);

        $this->assertJson($json);
        $this->assertEquals($expected, $json);
    }

    public function testDecode()
    {
        $stream = '{"name":"This is a name","settings":{"destination":{"uri":"http://localhost","auth":{"type":"basic"}}}}';

        $result = $this->instance->decode($stream);

        $this->assertInternalType('array', $result);
        $this->assertEquals($this->data, $result);
    }
}