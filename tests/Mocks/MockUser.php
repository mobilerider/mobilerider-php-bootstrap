<?php

namespace Mr\BootstrapTests\Mocks;

use Mr\Bootstrap\Model\BaseModel;

class MockUser extends BaseModel
{
    public static function getResource()
    {
        return 'user';
    }
}