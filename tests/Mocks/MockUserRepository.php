<?php

namespace Mr\BootstrapTests\Mocks;

use Mr\Bootstrap\Repository\BaseRepository;

class MockUserRepository extends BaseRepository
{
    public function getModelClass()
    {
        return MockUser::class;
    }
}