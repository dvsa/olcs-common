<?php

/**
 * PI Report Record Test
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

use Common\Service\Table\Formatter\PiReportRecord;

/**
 * PI Report Record Test
 *
 * @package CommonTest\Service\Table\Formatter
 */
class PiReportRecordTest extends TestCase
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
                        'licence',
                        [
                            'licence' => 123,
                        ]
                    )
                    ->andReturn('LIC_URL')
                    ->shouldReceive('fromRoute')
                    ->with(
                        'transport-manager/details',
                        [
                            'transportManager' => 3,
                        ]
                    )
                    ->andReturn('TM_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            $expected,
            PiReportRecord::format($data, [], $sm)
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
            'licence' => [
                [
                    'pi' => [
                        'case' => [
                            'licence' => [
                                'id' => 123,
                                'licNo' => 'AB1234567',
                                'status' => [
                                    'description' => 'lic status'
                                ]
                            ]
                        ]
                    ],
                ],
                '<a class="govuk-link" href="LIC_URL">AB1234567</a> (lic status)',
            ],
            'tm' => [
                [
                    'pi' => [
                        'case' => [
                            'transportManager' => [
                                'id' => 3,
                                'tmStatus' => [
                                    'description' => 'tm status'
                                ]
                            ]
                        ]
                    ],
                ],
                '<a class="govuk-link" href="TM_URL">TM 3</a> (tm status)',
            ],
            'other' => [
                [],
                '',
            ],
        ];
    }
}
