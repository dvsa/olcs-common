<?php

namespace CommonTest\Data\Object\Search;

use Common\Data\Object\Search\People;
use Common\RefData;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * @covers \Common\Data\Object\Search\SearchAbstract
 * @covers \Common\Data\Object\Search\People
 */
class PeopleTest extends SearchAbstractTest
{
    protected $class = People::class;

    /**
     * @dataProvider dataProviderTestDisqualifiedFormatter
     */
    public function testDisqualifiedFormatter($expected, $row)
    {
        $columns = $this->sut->getColumns();

        $this->assertSame($expected, $columns[6]['formatter']($row));
    }

    public function testGetDateRanges()
    {
        $dateRanges = $this->sut->getDateRanges();

        $this->assertCount(1, $dateRanges);

        $this->assertInstanceOf(
            \Common\Data\Object\Search\Aggregations\DateRange\DateOfBirthFromAndTo::class,
            $dateRanges[0]
        );
    }

    public function dataProviderTestDisqualifiedFormatter()
    {
        return [
            ['NA', ['foundAs' => 'Historical TM']],
            ['Yes', ['foundAs' => 'XX', 'disqualified' => 'Yes']],
            ['No', ['foundAs' => 'XX', 'disqualified' => 'No']],
        ];
    }
}
