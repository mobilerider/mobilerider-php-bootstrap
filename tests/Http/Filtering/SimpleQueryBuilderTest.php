<?php

namespace Mr\BootstrapTest\Http\Filtering;

use PHPUnit\Framework\TestCase;

use Mr\Bootstrap\Http\Filtering\SimpleQueryBuilder;

class SimpleQueryBuilderTest extends TestCase
{
    public function testConstruct()
    {   
        $qb = new SimpleQueryBuilder();

        $this->assertEquals([], $qb->toArray());
        $this->assertEquals('', (string) $qb);
    }

    public function testToArray()
    {
        $qb = new SimpleQueryBuilder(
            [
                ['name', 'John'],
                ['email', 'john@gmail.com'],
                ['first_name', 'like', '%john%']
            ]
        );

        $this->assertEquals(
            [
                'name' => 'John',
                'email' => 'john@gmail.com',
                // For now this ignores operators
                'first_name' => '%john%' 
            ], 
            $qb->toArray()
        );
    }

    public function testToString()
    {
        $qb = new SimpleQueryBuilder(
            [
                ['name', 'John'],
                ['email', 'john@gmail.com'],
                ['first_name', 'like', '%john%']
            ]
        );

        $this->assertEquals(
            http_build_query(
                [
                    'name' => 'John',
                    'email' => 'john@gmail.com',
                    'first_name' => '%john%' 
                ]
            ),
            (string) $qb
        );
    }
}