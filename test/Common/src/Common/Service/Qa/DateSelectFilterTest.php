<?php

namespace CommonTest\Service\Qa;

use Common\Service\Qa\DateSelectFilter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Filter\Exception\InvalidArgumentException;

/**
 * DateSelectFilterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class DateSelectFilterTest extends MockeryTestCase
{
    private $dateSelectFilter;

    public function setUp()
    {
        $this->dateSelectFilter = m::mock(DateSelectFilter::class)->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @dataProvider dpFilter
     */
    public function testFilter($dateStringFiltered, $expected)
    {
        $dateArray = [
            'year' => '2019',
            'month' => '4',
            'day' => '2'
        ];

        $dateStringUnfiltered = '2019-4-2';

        $this->dateSelectFilter->shouldReceive('callParentFilter')
            ->with($dateStringUnfiltered)
            ->once()
            ->andReturn($dateStringFiltered);

        $this->assertEquals(
            $expected,
            $this->dateSelectFilter->filter($dateArray)
        );
    }

    public function dpFilter()
    {
        return [
            ['2019-04-02', '2019-04-02'],
            [null, '2019-4-2'],
        ];
    }

    public function testFilterParentFilterRaisesException()
    {
        $dateArray = [
            'year' => 'xxxx',
            'month' => 'yy',
            'day' => 'zz'
        ];

        $dateStringUnfiltered = 'xxxx-yy-zz';

        $this->dateSelectFilter->shouldReceive('callParentFilter')
            ->with($dateStringUnfiltered)
            ->once()
            ->andThrow(InvalidArgumentException::class);

        $this->assertEquals(
            $dateStringUnfiltered,
            $this->dateSelectFilter->filter($dateArray)
        );
    }
}
