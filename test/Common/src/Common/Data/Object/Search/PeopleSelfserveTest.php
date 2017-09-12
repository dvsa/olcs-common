<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;

/**
 * @covers \Common\Data\Object\Search\PeopleSelfserve
 */
class PeopleSelfserveTest extends SearchAbstractTest
{
    protected $class = \Common\Data\Object\Search\PeopleSelfserve::class;

    public function testNameFormatter()
    {
        $row = [
            'personFullname' => 'Bob Smith',
        ];

        $column = [];

        $columns = $this->sut->getColumns();
        $this->assertSame('Bob Smith', $columns[3]['formatter']($row, $column));
    }
}
