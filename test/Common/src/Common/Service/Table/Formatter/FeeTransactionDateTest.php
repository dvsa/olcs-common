<?php

/**
 * Fee Transaction Date formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\FeeTransactionDate as Sut;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Fee Transaction Date formatter test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTransactionDateTest extends MockeryTestCase
{
    public function testFormat()
    {
        $data = [
            'child' => [
                'someDate' => '2015-09-01',
            ]
        ];

        $column = [
            'stack' => 'child->someDate',
        ];

        $expected = '01/09/2015';

        $sm = $this->createMock('\stdClass', array('get'));

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Stack')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getStackValue')
                    ->with(
                        $data,
                        ['child', 'someDate']
                    )
                    ->andReturn('2015-09-01')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals($expected, Sut::format($data, $column, $sm));
    }
}
