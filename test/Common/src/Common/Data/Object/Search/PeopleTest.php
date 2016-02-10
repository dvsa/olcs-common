<?php

namespace CommonTest\Data\Object\Search;

use Mockery as m;

/**
 * Class PeopleTest
 * @package CommonTest\Data\Object\Search
 */
class PeopleTest extends SearchAbstractTest
{
    protected $class = 'Common\Data\Object\Search\People';

    private $sut;

    public function setUp()
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
            ['<a href="http://URL">TM 123</a>', ['tmId' => 123, 'foundAs' => 'XX']],
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
            ['Bobby Smith', ['foundAs' => 'ZZ', 'personForename' => 'Bobby', 'personFamilyName' => 'Smith']],
            [
                '<a href="http://URL">Bobby Smith</a>',
                ['foundAs' => 'Historical TM', 'personForename' => 'Bobby', 'personFamilyName' => 'Smith', 'tmId' => 1]
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

    public function dataProviderTestDisqualifiedFormatter()
    {
        return [
            ['NA', ['foundAs' => 'Historical TM']],
            ['Yes', ['foundAs' => 'XX', 'disqualified' => 'Y']],
            ['No', ['foundAs' => 'XX', 'disqualified' => 'N']],
        ];
    }
}
