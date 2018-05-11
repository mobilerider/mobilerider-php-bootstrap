<?php

namespace Mr\BootstrapTest\Http\Filtering;

use PHPUnit\Framework\TestCase;
use Mr\Bootstrap\Http\Filtering\PrettusL5QueryBuilder;

class PrettusL5QueryBuilderTest extends TestCase
{
    public function testToArray()
    {
        $qb = new PrettusL5QueryBuilder([
            ['name', 'John'],
            ['email', 'john@gmail.com'],
            ['first_name', 'like', '%john%']
        ]);

        $this->assertEquals([
            'search' => 'name:John;email:john@gmail.com;first_name:%john%',
            'searchFields' => 'first_name:like'
        ], $qb->toArray());
    }
}