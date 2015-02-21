<?php

/**
 * Application Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use PHPUnit_Framework_TestCase;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Review\ApplicationPsvOcTotalAuthReviewService;

/**
 * Application Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPsvOcTotalAuthReviewServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationPsvOcTotalAuthReviewService();
    }

    /**
     * @dataProvider licenceTypeProvider
     */
    public function testGetConfigFromData($licenceType, $expected)
    {
        $data = [
            'licenceType' => ['id' => $licenceType],
            'totAuthSmallVehicles' => 10,
            'totAuthMediumVehicles' => 20,
            'totAuthLargeVehicles' => 30,
            'totAuthVehicles' => 60,
            'totCommunityLicences' => 50
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function licenceTypeProvider()
    {
        return [
            'standard national' => [
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-small',
                                'value' => 10
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-medium',
                                'value' => 20
                            ],
                            'large' => [
                                'label' => 'review-operating-centres-authorisation-vehicles-large',
                                'value' => 30
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ]
                        ]
                    ]
                ]
            ],
            'standard international' => [
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-small',
                                'value' => 10
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-medium',
                                'value' => 20
                            ],
                            'large' => [
                                'label' => 'review-operating-centres-authorisation-vehicles-large',
                                'value' => 30
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-community-licences',
                                'value' => 50
                            ]
                        ]
                    ]
                ]
            ],
            'restricted' => [
                LicenceEntityService::LICENCE_TYPE_RESTRICTED,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-small',
                                'value' => 10
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-medium',
                                'value' => 20
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-community-licences',
                                'value' => 50
                            ]
                        ]
                    ]
                ]
            ],
            'special restricted' => [
                LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                [
                    'header' => 'review-operating-centres-authorisation-title',
                    'multiItems' => [
                        [
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-small',
                                'value' => 10
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles-medium',
                                'value' => 20
                            ],
                            [
                                'label' => 'review-operating-centres-authorisation-vehicles',
                                'value' => 60
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }
}
