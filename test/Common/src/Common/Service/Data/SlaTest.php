<?php

namespace CommonTest\Service\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\Sla;
use Mockery as m;

/**
 * Class Sla Test
 * @package CommonTest\Service
 */
class SlaTest extends MockeryTestCase
{
    public function testGetServiceName()
    {
        $sut = new Sla();
        $this->assertEquals('Sla', $sut->getServiceName());
    }

    /**
     * @dataProvider provideTargetDate
     */
    public function testGetTargetDate($date, $context, $exception = false)
    {
        $field = 'desisionLetterSentDate';

        $tdp = $this->getMock('Common\Util\DateTimeProcessor', ['calculateDate']);
        $tdp->expects($this->any())->method('calculateDate')->will($this->returnValue($date));

        $busRules = [
            [
                'field' => $field,
                'compareTo' => 'agreedDate',
                'days' => '50',
                'category' => 'pi',
                'effectiveFrom' => '2010-10-10',
                'effectiveTo' => '2014-10-09',
                'weekend' => false,
                'publicHoliday' => false
            ],
            [
                'field' => $field,
                'compareTo' => 'agreedDate',
                'days' => '35',
                'category' => 'pi',
                'effectiveFrom' => '2014-10-10',
                'effectiveTo' => null,
                'weekend' => false,
                'publicHoliday' => false
            ]
        ];

        $sut = new Sla();
        $sut->setTimeDateProcessor($tdp);
        $sut->setData('pi', $busRules);

        $passed = !$exception;

        try {
            $result = $sut->getTargetDate('pi', $field, $context);
        } catch (\LogicException $e) {
            if ('No rule exists for this context' === $e->getMessage()) {
                $passed = true;
            }
        }

        $this->assertTrue($passed, 'Expected exception not thrown or message didn\'t match');

        if (!$exception) {
            $this->assertEquals($date, $result);
        }
    }

    public function provideTargetDate()
    {
        return [
            [
                '2014-11-19 00:00:00',
                ['agreedDate' => '2014-10-15T00:00:00+0000']
            ],
            [
                '2014-11-19',
                ['agreedDate' => '2014-10-15']
            ],
            [
                '2012-11-20',
                ['agreedDate' => '2012-10-01']
            ],
            [
                '2012-11-20',
                ['agreedDate' => '2000-10-01'],
                true
            ]
        ];
    }

    /**
     * @dataProvider provideFetchBusRulesData
     * @param $data
     * @param $expected
     */
    public function testFetchBusRules($data, $expected)
    {
        $category = 'pi';

        $mockRestClient = m::mock('Common\Util\RestClient');
        $mockRestClient
            ->shouldReceive('get')
            ->once()
            ->with('', ['limit' => 1000, 'category' => $category])
            ->andReturn($data);

        $sut = new Sla();
        $sut->setRestClient($mockRestClient);

        //ensure data is cached
        $this->assertEquals($expected, $sut->fetchBusRules($category));
        $this->assertEquals($expected, $sut->fetchBusRules($category));
    }

    public function provideFetchBusRulesData()
    {
        return [
            [false, null],
            [['Results' => $this->getSingleSource()], $this->getSingleSource()]
        ];
    }

    /**
     * @return array
     */
    protected function getSingleSource()
    {
        $source = [
            ['category' => 'pi', 'field' => 'decision_letter_sent_date', 'refField' => 'agreed_date', 'days' => '21'],
        ];
        return $source;
    }

    /**
     * Does a simple test of the add days to date method.
     */
    public function testDateAdd()
    {
        $sut = new Sla();

        $date = '2014-02-27';

        $outDate = '2014-03-08';

        $this->assertEquals($outDate, $sut->dateAddDays($date, '9'));
    }
}
