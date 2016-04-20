<?php

/**
 * Case Link Test
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;

use Common\Service\Table\Formatter\CaseLink;

/**
 * Case Link Test
 *
 * @package CommonTest\Service\Table\Formatter
 */
class CaseLinkTest extends \PHPUnit_Framework_TestCase
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
