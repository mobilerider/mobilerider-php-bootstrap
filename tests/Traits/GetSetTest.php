<?php

namespace Mr\BootstrapTest\Traits;

use PHPUnit\Framework\TestCase;
use Mr\Bootstrap\Traits\GetSet;

class GetSetSample
{
    use GetSet;
}


class GetSetTest extends TestCase
{
    /**
     * @var GetSetSample
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
        $this->instance = new GetSetSample();
        $this->instance->fill($this->data);
    }

    public function testHas()
    {
        $this->assertTrue($this->instance->has('name'));

        $this->assertTrue($this->instance->has('settings.destination'));

        $this->assertTrue($this->instance->has('settings.destination.uri'));

        $this->assertTrue($this->instance->has('settings.destination.auth.type'));

        $this->assertFalse($this->instance->has('invalid'));

        $this->assertFalse($this->instance->has('settings.invalid'));

        $this->assertFalse($this->instance->has('path.that.does.not.exists'));

    }

    public function testGet()
    {
        $this->assertEquals($this->data['name'], $this->instance->get('name'));

        $this->assertEquals($this->data['settings']['destination'], $this->instance->get('settings.destination'));

        $this->assertEquals($this->data['settings']['destination']['uri'], $this->instance->get('settings.destination.uri'));

        $this->assertEquals($this->data['settings']['destination']['auth']['type'], $this->instance->get('settings.destination.auth.type'));
    }

    public function testSet()
    {
        $newName = 'This is a new name';
        $newDestination = [ 'uri' => 'Testings destination' ];
        $newUri = 'https://mobilerider.com';
        $newAuthType = 'cross_account';
        $newValue = 'new value';

        $this->instance->set('name', $newName);

        $this->assertEquals($newName, $this->instance->get('name'));

        $this->instance->set('settings.destination', $newDestination);

        $this->assertEquals($newDestination, $this->instance->get('settings.destination'));

        $this->instance->set('settings.destination.uri', $newUri);

        $this->assertEquals($newUri, $this->instance->get('settings.destination.uri'));

        $this->instance->set('settings.destination.auth.type', $newAuthType);

        $this->assertEquals($newAuthType, $this->instance->get('settings.destination.auth.type'));

        $this->instance->set('path.that.does.not.exists', $newValue);

        $this->assertEquals($newValue, $this->instance->get('path.that.does.not.exists'));
    }
}