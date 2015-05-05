<?php

/**
 * Transport Managers Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\TransportManagersReviewService;

/**
 * Transport Managers Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagersReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new TransportManagersReviewService();
    }

    public function testGetConfigFromData()
    {
        $data = [
            [
                'transportManager' => [
                    'homeCd' => [
                        'emailAddress' => 'foo@bar.com',
                        'person' => [
                            'birthDate' => '1989-08-23',
                            'forename' => 'foo',
                            'familyName' => 'bar',
                            'title' => [
                                'description' => 'Mr'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'transportManager' => [
                    'homeCd' => [
                        'emailAddress' => 'bar@foo.com',
                        'person' => [
                            'birthDate' => '1991-08-23',
                            'forename' => 'bar',
                            'familyName' => 'foo',
                            'title' => [
                                'description' => 'Mrs'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $expected = [
            [
                'header' => 'Mr foo bar',
                'multiItems' => [
                    [
                        [
                            'label' => 'review-transport-manager-email',
                            'value' => 'foo@bar.com'
                        ],
                        [
                            'label' => 'review-transport-manager-dob',
                            'value' => '23/08/1989'
                        ]
                    ]
                ]
            ],
            [
                'header' => 'Mrs bar foo',
                'multiItems' => [
                    [
                        [
                            'label' => 'review-transport-manager-email',
                            'value' => 'bar@foo.com'
                        ],
                        [
                            'label' => 'review-transport-manager-dob',
                            'value' => '23/08/1991'
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
