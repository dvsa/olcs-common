<?php

/**
 * PI Report Name Test
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;

use Common\Service\Table\Formatter\PiReportName;

/**
 * PI Report Name Test
 *
 * @package CommonTest\Service\Table\Formatter
 */
class PiReportNameTest extends \PHPUnit_Framework_TestCase
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
                        'operator/business-details',
                        [
                            'organisation' => 456,
                        ]
                    )
                    ->andReturn('ORG_URL')
                    ->getMock()
            )
            ->getMock();

        $this->assertEquals(
            $expected,
            PiReportName::format($data, [], $sm)
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
                                'organisation' => [
                                    'id' => 456,
                                    'name' => 'Org name',
                                ]
                            ]
                        ]
                    ],
                ],
                '<a href="ORG_URL">Org name</a>',
            ],
            'tm' => [
                [
                    'pi' => [
                        'case' => [
                            'transportManager' => [
                                'homeCd' => [
                                    'person' => [
                                        'forename' => 'TM',
                                        'familyName' => 'Name',
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
                'TM Name',
            ],
            'other' => [
                [],
                '',
            ],
        ];
    }
}
