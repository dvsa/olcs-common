<?php

/**
 * Application Review Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;
use Common\Controller\Lva\Adapters\ApplicationReviewAdapter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Application Review Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationReviewAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationReviewAdapter();

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
            'business_type',
            // @NOTE As there is no service found, this section will be ignored
            'business_details'
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
        $stubbedBtConfig = ['items' => 'bar'];

        // Mocks
        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $mockTolService = m::mock();
        $this->sm->setService('Review\ApplicationTypeOfLicence', $mockTolService);
        $mockBtService = m::mock();
        $this->sm->setService('Review\ApplicationBusinessType', $mockBtService);

        // Expectations
        $mockApplicationEntity->shouldReceive('getReviewDataForApplication')
            ->with($id, $relevantSections)
            ->andReturn($stubbedReviewData);

        $mockTolService->shouldReceive('getConfigFromData')
            ->with($stubbedReviewData)
            ->andReturn($stubbedTolConfig);

        $mockBtService->shouldReceive('getConfigFromData')
            ->with($stubbedReviewData)
            ->andReturn($stubbedBtConfig);

        $return = $this->sut->getSectionData($id, $relevantSections);

        $expected = [
            'reviewTitle' => $expectedTitle,
            'sections' => [
                [
                    'header' => 'review-type_of_licence',
                    'config' => $stubbedTolConfig
                ],
                [
                    'header' => 'review-business_type',
                    'config' => $stubbedBtConfig
                ],
                [
                    'header' => 'review-business_details',
                    'config' => null
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
                'application-review-title-gv'
            ],
            [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                'application-review-title-psv'
            ],
            [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'application-review-title-psv-sr'
            ]
        ];
    }
}
