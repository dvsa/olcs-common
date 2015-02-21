<?php

/**
 * Variation Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Review\VariationPsvOcTotalAuthReviewService;

/**
 * Variation Psv Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPsvOcTotalAuthReviewServiceTest extends MockeryTestCase
{

    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationPsvOcTotalAuthReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataWithoutChanges()
    {
        $data = [
            'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 4,
            'totAuthVehicles' => 10,
            'licence' => [
                'totAuthSmallVehicles' => 3,
                'totAuthMediumVehicles' => 3,
                'totAuthLargeVehicles' => 4,
                'totAuthVehicles' => 10
            ]
        ];

        $this->assertNull($this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChanges()
    {
        $data = [
            'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthSmallVehicles' => 2,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 4,
            'totAuthVehicles' => 9,
            'licence' => [
                'totAuthSmallVehicles' => 1,
                'totAuthMediumVehicles' => 1,
                'totAuthLargeVehicles' => 1,
                'totAuthVehicles' => 3
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-small',
                        'value' => 'increased from 1 to 2'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-medium',
                        'value' => 'increased from 1 to 3'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-large',
                        'value' => 'increased from 1 to 4'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'increased from 3 to 9'
                    ]
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 2])
            ->andReturn('increased from 1 to 2')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 3])
            ->andReturn('increased from 1 to 3')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 4])
            ->andReturn('increased from 1 to 4')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [3, 9])
            ->andReturn('increased from 3 to 9');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChangesWithStandardInternational()
    {
        $data = [
            'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'totAuthSmallVehicles' => 2,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 4,
            'totAuthVehicles' => 9,
            'totCommunityLicences' => 5,
            'licence' => [
                'totAuthSmallVehicles' => 1,
                'totAuthMediumVehicles' => 1,
                'totAuthLargeVehicles' => 1,
                'totAuthVehicles' => 3,
                'totCommunityLicences' => 1,
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-small',
                        'value' => 'increased from 1 to 2'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-medium',
                        'value' => 'increased from 1 to 3'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles-large',
                        'value' => 'increased from 1 to 4'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'increased from 3 to 9'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-community-licences',
                        'value' => 'increased from 1 to 5'
                    ]
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 2])
            ->andReturn('increased from 1 to 2')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 3])
            ->andReturn('increased from 1 to 3')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 4])
            ->andReturn('increased from 1 to 4')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [3, 9])
            ->andReturn('increased from 3 to 9')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 5])
            ->andReturn('increased from 1 to 5');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
