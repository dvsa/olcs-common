<?php

/**
 * Case Link Test
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

use Common\Service\Table\Formatter\CaseLink;

/**
 * Case Link Test
 *
 * @package CommonTest\Service\Table\Formatter
 */
class CaseLinkTest extends TestCase
{
    /**
     * Test the format method
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expected)
    {
        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                    ->shouldReceive('fromRoute')
                    ->with(
                        'case',
                        [
                            'case' => 69,
                        ]
                    )
                    ->andReturn('CASE_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            $expected,
            CaseLink::format($data, [], $sm)
        );
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'case' => [
                [
                    'id' => 69
                ],
                '<a href="CASE_URL">69</a>',
            ],
            'other' => [
                [],
                '',
            ],
        ];
    }
}
