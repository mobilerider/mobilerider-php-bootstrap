<?php

namespace Mr\BootstrapTest\Traits;

use Mr\Bootstrap\Data\JsonTransformer;
use PHPUnit\Framework\TestCase;


class JsonTransfomerTest extends TestCase
{
    /**
     * @var JsonTransformer
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
        $this->instance = new JsonTransformer();
    }

    public function testTransform()
    {
        // Do not escape slashes by default
        $expected = '{"name":"This is a name","settings":{"destination":{"uri":"http://localhost","auth":{"type":"basic"}}}}';

        $json = $this->instance->transform($this->data);

        $this->assertJson($json);
        $this->assertEquals($expected, $json);
    }

    public function testToArray()
    {
        $stream = '{"name":"This is a name","settings":{"destination":{"uri":"http://localhost","auth":{"type":"basic"}}}}';

        $result = $this->instance->toArray($stream);

        $this->assertInternalType('array', $result);
        $this->assertEquals($this->data, $result);
    }
}