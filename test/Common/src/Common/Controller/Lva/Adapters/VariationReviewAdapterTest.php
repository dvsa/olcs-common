<?php

/**
 * Variation Review Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;
use Common\Controller\Lva\Adapters\VariationReviewAdapter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\VariationCompletionEntityService;

/**
 * Variation Review Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationReviewAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationReviewAdapter();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetSectionData
     */
    public function testGetSectionData($operatorType, $licenceType, $expectedTitle)
    {
        // Params
        $id = 123;
        $relevantSections = [
            'type_of_licence',
            // <<< @NOTE These sections get filtered out
            'business_type',
            'business_details',
            'addresses',
            'safety',
            'community_licences',
            // <<< END
            'operating_centres',
            // @NOTE this section should be ignored, as it has not been updated
            'vehicles'
        ];
        $filteredSections = [
            'type_of_licence',
            'operating_centres'
        ];
        $stubbedReviewData = [
            'goodsOrPsv' => [
                'id' => $operatorType
            ],
            'licenceType' => [
                'id' => $licenceType
            ]
        ];
        $stubbedTolConfig = ['items' => 'foo'];
        $stubbedOcConfig = ['items' => 'bar'];
        $stubbedCompletions = [
            'type_of_licence' => VariationCompletionEntityService::STATUS_UPDATED,
            'operating_centres' => VariationCompletionEntityService::STATUS_UPDATED,
            'vehicles' => VariationCompletionEntityService::STATUS_UNCHANGED
        ];

        // Mocks
        $mockVariationCompletion = m::mock();
        $this->sm->setService('Entity\VariationCompletion', $mockVariationCompletion);
        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $mockTolService = m::mock();
        $this->sm->setService('Review\VariationTypeOfLicence', $mockTolService);
        $mockOcService = m::mock();
        $this->sm->setService('Review\VariationOperatingCentres', $mockOcService);
        $mockVehicleService = m::mock();
        $this->sm->setService('Review\VariationVehicles', $mockVehicleService);

        // Expectations
        $mockApplicationEntity->shouldReceive('getReviewDataForVariation')
            ->with($id, $filteredSections)
            ->andReturn($stubbedReviewData);

        $mockTolService->shouldReceive('getConfigFromData')
            ->with($stubbedReviewData)
            ->andReturn($stubbedTolConfig);

        $mockOcService->shouldReceive('getConfigFromData')
            ->with($stubbedReviewData)
            ->andReturn($stubbedOcConfig);

        $mockVariationCompletion->shouldReceive('getCompletionStatuses')
            ->with($id)
            ->andReturn($stubbedCompletions);

        $return = $this->sut->getSectionData($id, $relevantSections);

        $expected = [
            'reviewTitle' => $expectedTitle,
            'sections' => [
                [
                    'header' => 'review-type_of_licence',
                    'config' => $stubbedTolConfig
                ],
                [
                    'header' => 'review-operating_centres',
                    'config' => $stubbedOcConfig
                ]
            ]
        ];

        $this->assertEquals($expected, $return);
    }

    public function providerGetSectionData()
    {
        return [
            [
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'variation-review-title-gv'
            ],
            [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'variation-review-title-psv'
            ],
            [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'variation-review-title-psv'
            ]
        ];
    }
}
