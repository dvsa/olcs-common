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
     * @dataProvider dataProviderTestRecordFormatter
     */
    public function testRecordFormatter($expected, $row, $isIrhpAdmin)
    {
        $column = [];

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
        $serviceLocator->shouldReceive('get')->with('Helper\Url')->andReturn(
            m::mock()->shouldReceive('fromRoute')->andReturn('http://URL')->getMock()
        );

        $authService = m::mock(AuthorizationService::class);
        $authService->shouldReceive('isGranted')
            ->with(RefData::PERMISSION_INTERNAL_IRHP_ADMIN)
            ->once()
            ->andReturn($isIrhpAdmin);

        $serviceLocator->shouldReceive('get')
            ->with(AuthorizationService::class)
            ->once()
            ->andReturn($authService);

        $columns = $this->sut->getColumns();
        $this->assertSame($expected, $columns[1]['formatter']($row, $column, $serviceLocator));
    }

    public function dataProviderTestRecordFormatter()
    {
        return [
            // expected, row, isIrhpAdmin
            ['', [], false],
            ['', [], true],
            [
                '<a href="http://URL">OB123&gt;</a> / <a href="http://URL">456&gt;</a>',
                [
                    'licNo' => 'OB123>',
                    'applicationId' => '456>'
                ],
                false,
            ],
            [
                'OB123&gt; / 456&gt;',
                [
                    'licNo' => 'OB123>',
                    'applicationId' => '456>'
                ],
                true,
            ],
            [
                '<a href="http://URL">TM 123&gt;</a>',
                [
                    'tmId' => '123>',
                    'foundAs' => 'XX'
                ],
                false,
            ],
            [
                'TM 123&gt;',
                [
                    'tmId' => '123>',
                    'foundAs' => 'XX'
                ],
                true,
            ],
            [
                '<a href="http://URL">TM 123&gt;</a> / <a href="http://URL">OB123&gt;</a>',
                [
                    'tmId' => '123>',
                    'licNo' => 'OB123>',
                    'foundAs' => 'XX'
                ],
                false,
            ],
            [
                'TM 123&gt; / OB123&gt;',
                [
                    'tmId' => '123>',
                    'licNo' => 'OB123>',
                    'foundAs' => 'XX'
                ],
                true,
            ],
            [
                '<a href="http://URL">OB123&gt;</a>, LIC_TYPE_DESC&gt;<br />LIC_STATUS_DESC&gt;',
                [
                    'licId' => '123>',
                    'licNo' => 'OB123>',
                    'licTypeDesc' => 'LIC_TYPE_DESC>',
                    'licStatusDesc' => 'LIC_STATUS_DESC>',
                ],
                false,
            ],
            [
                'OB123&gt;, LIC_TYPE_DESC&gt;<br />LIC_STATUS_DESC&gt;',
                [
                    'licId' => '123>',
                    'licNo' => 'OB123>',
                    'licTypeDesc' => 'LIC_TYPE_DESC>',
                    'licStatusDesc' => 'LIC_STATUS_DESC>',
                ],
                true,
            ],
            [
                '<a href="http://URL">LIC_NO&gt;</a>',
                [
                    'licNo' => 'LIC_NO>',
                ],
                false,
            ],
            [
                'LIC_NO&gt;',
                [
                    'licNo' => 'LIC_NO>',
                ],
                true,
            ],
            [
                '<a href="http://URL">123&gt;</a>, APP_STATUS_DESC&gt;',
                [
                    'applicationId' => '123>',
                    'appStatusDesc' => 'APP_STATUS_DESC>',
                ],
                false,
            ],
            [
                '123&gt;, APP_STATUS_DESC&gt;',
                [
                    'applicationId' => '123>',
                    'appStatusDesc' => 'APP_STATUS_DESC>',
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderTestNameFormatter
     */
    public function testNameFormatter($expected, $row)
    {
        $column = [];

        $serviceLocator = m::mock(ServiceLocatorInterface::class);
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
