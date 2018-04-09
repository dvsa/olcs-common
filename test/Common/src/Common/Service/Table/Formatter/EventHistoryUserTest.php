<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\EventHistoryUser;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Event history user formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class EventHistoryUserTest extends MockeryTestCase
{
    /**
     * Test the format method
     *
     * @dataProvider provider
     */
    public function testFormat($data, $expectedOutput)
    {
        $sm = m::mock();

        if (isset($data['user']['team'])) {
            $sm->shouldReceive('get')
                ->with('Translator')
                ->andReturn(
                    m::mock()
                        ->shouldReceive('translate')
                        ->with('internal.marker')
                        ->andReturn('(internal)')
                        ->once()
                        ->getMock()
                )
                ->once()
                ->getMock();
        }

        $this->assertEquals($expectedOutput, EventHistoryUser::format($data, [], $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'external with name' => [
                [
                    'user' => [
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'Foo',
                                'familyName' => 'Bar'
                            ]
                        ]
                    ],
                    'changeMadeBy' => null,
                ],
                'Foo Bar',
            ],
            'internal with name' => [
                [
                    'user' => [
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'Foo',
                                'familyName' => 'Bar'
                            ]
                        ],
                        'team' => 'some team'
                    ],
                    'changeMadeBy' => null,
                ],
                'Foo Bar (internal)',
            ],
            'external with no name' => [
                [
                    'user' => [
                        'loginId' => 'cake'
                    ],
                    'changeMadeBy' => null,
                ],
                'cake',
            ],
            'internal with no name' => [
                [
                    'user' => [
                        'loginId' => 'cake',
                        'team' => 'some team'
                    ],
                    'changeMadeBy' => null,
                ],
                'cake (internal)',
            ],
            'internal with error' => [
                [
                    'user' => [
                        'contactDetails' => [
                            'person' => 'something wrong here'
                        ],
                        'team' => 'some team',
                        'loginId' => 'cake'
                    ],
                    'changeMadeBy' => null,
                ],
                'cake (internal)',
            ],
            'external with change made by' => [
                [
                    'user' => [
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'Foo',
                                'familyName' => 'Bar'
                            ]
                        ]
                    ],
                    'changeMadeBy' => 'Name Surname',
                ],
                'Name Surname',
            ],
            'internal with change made by' => [
                [
                    'user' => [
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'Foo',
                                'familyName' => 'Bar'
                            ]
                        ],
                        'team' => 'some team'
                    ],
                    'changeMadeBy' => 'Name Surname',
                ],
                'Name Surname (internal)',
            ],
        ];
    }
}
