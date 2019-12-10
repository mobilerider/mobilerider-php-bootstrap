<?php

namespace Mr\BootstrapTest\Http\Filtering;

use PHPUnit\Framework\TestCase;

use Mr\Bootstrap\Http\Filtering\MrApiQueryBuilder;

class MrApiQueryBuilderTest extends TestCase
{
    public function testConstruct()
    {   
        $qb = new MrApiQueryBuilder();

        $this->assertEquals([], $qb->toArray());
        $this->assertEquals('', (string) $qb);
    }

    public function testToArray()
    {
        $qb = new MrApiQueryBuilder(
            [
                ['name', 'John'],
                ['email', 'contains', 'gmail.com'],
                ['first_name', 'like', '%john%']
            ]
        );

        $this->assertEquals(
            [
                'name' => 'John',
                'email__contains' => 'gmail.com',
                // For now this ignores operators
                'first_name__like' => '%john%' 
            ], 
            $qb->toArray()
        );
    }

    public function testToString()
    {
        $qb = new MrApiQueryBuilder(
            [
                ['name', 'John'],
                ['email', 'contains', 'gmail.com'],
                ['first_name', 'like', '%john%']
            ]
        );

        $this->assertEquals(
            http_build_query(
                [
                    'name' => 'John',
                    'email__contains' => 'gmail.com',
                    // For now this ignores operators
                    'first_name__like' => '%john%'
                ]
            ),
            (string) $qb
        );
    }
}