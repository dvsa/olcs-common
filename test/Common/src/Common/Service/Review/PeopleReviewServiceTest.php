<?php

/**
 * People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\PeopleReviewService;
use Common\Service\Entity\OrganisationEntityService;

/**
 * People Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PeopleReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new PeopleReviewService();
    }

    /**
     * @dataProvider provider
     */
    public function testGetConfigFromData($data, $showPosition, $expected)
    {
        $this->assertEquals($expected, $this->sut->getConfigFromData($data, $showPosition));
    }

    /**
     * @dataProvider providerShouldShowPosition
     */
    public function testShouldShowPosition($orgType, $expected)
    {
        $data = [
            'licence' => [
                'organisation' => [
                    'type' => [
                        'id' => $orgType
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $this->sut->shouldShowPosition($data));
    }

    public function providerShouldShowPosition()
    {
        return [
            [
                OrganisationEntityService::ORG_TYPE_OTHER,
                true
            ],
            [
                OrganisationEntityService::ORG_TYPE_SOLE_TRADER,
                false
            ],
            [
                OrganisationEntityService::ORG_TYPE_PARTNERSHIP,
                false
            ],
            [
                OrganisationEntityService::ORG_TYPE_LLP,
                false
            ],
            [
                OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY,
                false
            ]
        ];
    }

    public function provider()
    {
        return [
            [
                [
                    'position' => 'The boss',
                    'person' => [
                        'forename' => 'Bob',
                        'familyName' => 'Smith',
                        'title' => 'Mr',
                        'otherName' => 'Robert',
                        'birthDate' => '1989-08-23'
                    ]
                ],
                false,
                [
                    'header' => 'Bob Smith',
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-people-person-title',
                                'value' => 'Mr'
                            ],
                            [
                                'label' => 'application-review-people-person-firstname',
                                'value' => 'Bob'
                            ],
                            [
                                'label' => 'application-review-people-person-lastname',
                                'value' => 'Smith'
                            ],
                            [
                                'label' => 'application-review-people-person-othername',
                                'value' => 'Robert'
                            ],
                            [
                                'label' => 'application-review-people-person-dob',
                                'value' => '23/08/1989'
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'position' => 'The boss',
                    'person' => [
                        'forename' => 'Bob',
                        'familyName' => 'Smith',
                        'title' => 'Mr',
                        'otherName' => 'Robert',
                        'birthDate' => '1989-08-23'
                    ]
                ],
                true,
                [
                    'header' => 'Bob Smith',
                    'multiItems' => [
                        [
                            [
                                'label' => 'application-review-people-person-title',
                                'value' => 'Mr'
                            ],
                            [
                                'label' => 'application-review-people-person-firstname',
                                'value' => 'Bob'
                            ],
                            [
                                'label' => 'application-review-people-person-lastname',
                                'value' => 'Smith'
                            ],
                            [
                                'label' => 'application-review-people-person-othername',
                                'value' => 'Robert'
                            ],
                            [
                                'label' => 'application-review-people-person-dob',
                                'value' => '23/08/1989'
                            ],
                            [
                                'label' => 'application-review-people-person-position',
                                'value' => 'The boss'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
