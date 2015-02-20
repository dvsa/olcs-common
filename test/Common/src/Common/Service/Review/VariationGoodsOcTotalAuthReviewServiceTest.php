<?php

/**
 * Variation Goods Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Review\VariationGoodsOcTotalAuthReviewService;

/**
 * Variation Goods Oc Total Auth Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsOcTotalAuthReviewServiceTest extends MockeryTestCase
{

    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationGoodsOcTotalAuthReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataWithoutChanges()
    {
        $data = [
            'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
            'licence' => [
                'totAuthVehicles' => 10,
                'totAuthTrailers' => 10
            ]
        ];

        $this->assertNull($this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChanges()
    {
        $data = [
            'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL],
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
            'licence' => [
                'totAuthVehicles' => 20,
                'totAuthTrailers' => 5
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'decreased from 20 to 10'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => 'increased from 5 to 10'
                    ]
                ]
            ]
        ];

        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translateReplace')
            ->with('review-value-decreased', [20, 10])
            ->andReturn('decreased from 20 to 10')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [5, 10])
            ->andReturn('increased from 5 to 10');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataWithChangesWithCommunityLicences()
    {
        $data = [
            'licenceType' => ['id' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 10,
            'totCommunityLicences' => 5,
            'licence' => [
                'totAuthVehicles' => 20,
                'totAuthTrailers' => 5,
                'totCommunityLicences' => 1,
            ]
        ];

        $expected = [
            'header' => 'review-operating-centres-authorisation-title',
            'multiItems' => [
                [
                    [
                        'label' => 'review-operating-centres-authorisation-vehicles',
                        'value' => 'decreased from 20 to 10'
                    ],
                    [
                        'label' => 'review-operating-centres-authorisation-trailers',
                        'value' => 'increased from 5 to 10'
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
            ->with('review-value-decreased', [20, 10])
            ->andReturn('decreased from 20 to 10')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [5, 10])
            ->andReturn('increased from 5 to 10')
            ->shouldReceive('translateReplace')
            ->with('review-value-increased', [1, 5])
            ->andReturn('increased from 1 to 5');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
