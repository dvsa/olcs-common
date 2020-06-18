<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;

/**
 * @covers \Common\Data\Object\Search\People
 */
class PeopleTest extends SearchAbstractTest
{
    protected $class = \Common\Data\Object\Search\People::class;

    public function setUp(): void
    {
        $this->sut = new \Common\Data\Object\Search\People();

        parent::setUp();
    }

    /**
     * @dataProvider dataProviderTestRecordFormatter
     */
    public function testRecordFormatter($expected, $row)
    {
        $column = [];
        $serviceLocator = m::mock();

        $serviceLocator->shouldReceive('get')->with('Helper\Url')->andReturn(
            m::mock()->shouldReceive('fromRoute')->andReturn('http://URL')->getMock()
        );

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[1]['formatter']($row, $column, $serviceLocator));
    }

    public function dataProviderTestRecordFormatter()
    {
        return [
            // expected, row
            ['', []],
            [
                '<a href="http://URL">TM 123</a>',
                [
                    'tmId' => 123,
                    'foundAs' => 'XX'
                ]
            ],
            [
                '<a href="http://URL">TM 123</a> / <a href="http://URL">OB123</a>',
                [
                    'tmId' => 123,
                    'licNo' => 'OB123',
                    'foundAs' => 'XX'
                ]
            ],
            [
                '<a href="http://URL">LIC_NO</a>, LT_DESC<br />LS_DESC',
                [
                    'licId' => 123,
                    'foundAs' => 'XX',
                    'licNo' => 'LIC_NO',
                    'licTypeDesc' => 'LT_DESC',
                    'licStatusDesc' => 'LS_DESC'
                ]
            ],
            [
                '<a href="http://URL">LIC_NO</a>',
                [
                    'foundAs' => 'XX',
                    'licNo' => 'LIC_NO',
                ]
            ],
            [
                '<a href="http://URL">OB123</a> / <a href="http://URL">456</a>',
                [
                    'licId' => 123,
                    'licNo' => 'OB123',
                    'applicationId' => 456
                ]
            ],
            [
                '<a href="http://URL">OB123</a>, LIC_TYPE_DESC<br />LIC_STATUS_DESC',
                [
                    'licId' => 123,
                    'licNo' => 'OB123',
                    'licTypeDesc' => 'LIC_TYPE_DESC',
                    'licStatusDesc' => 'LIC_STATUS_DESC',
                ]
            ],
            [
                '<a href="http://URL">LIC_NO</a>',
                [
                    'licNo' => 'LIC_NO',
                ]
            ],
            [
                '<a href="http://URL">123</a>, APP_STATUS_DESC',
                [
                    'applicationId' => 123,
                    'appStatusDesc' => 'APP_STATUS_DESC',
                ]
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestNameFormatter
     */
    public function testNameFormatter($expected, $row)
    {
        $column = [];
        $serviceLocator = m::mock();

        $serviceLocator->shouldReceive('get')->with('Helper\Url')->andReturn(
            m::mock()->shouldReceive('fromRoute')->andReturn('http://URL')->getMock()
        );

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[2]['formatter']($row, $column, $serviceLocator));
    }

    public function dataProviderTestNameFormatter()
    {
        return [
            ['Bobby Smith', ['foundAs' => 'ZZ', 'personFullname' => 'Bobby Smith']],
            [
                '<a href="http://URL">Bobby Smith</a>',
                ['foundAs' => 'Historical TM', 'personFullname' => 'Bobby Smith', 'tmId' => 1]
            ],
        ];
    }

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
