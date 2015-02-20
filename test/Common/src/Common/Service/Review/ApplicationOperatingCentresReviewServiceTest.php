<?php

/**
 * Application Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Review\ApplicationOperatingCentresReviewService;

/**
 * Application Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentresReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationOperatingCentresReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider psvProvider
     */
    public function testGetConfigFromDataWithEmptyOcList($licenceCat, $expectedOcService, $expectedTaService)
    {
        $data = [
            'goodsOrPsv' => ['id' => $licenceCat],
            'operatingCentres' => []
        ];
        $expected = [
            'subSections' => [
                [
                    'mainItems' => [
                        'TACONFIG',
                        'TOTAL_AUTH_CONFIG'
                    ]
                ]
            ]
        ];

        // Mocks
        $mockOcService = m::mock();
        $mockTotalAuthService = m::mock();
        $mockTaService = m::mock();
        $this->sm->setService('Review\\' . $expectedOcService, $mockOcService);
        $this->sm->setService('Review\\' . $expectedTaService, $mockTotalAuthService);
        $this->sm->setService('Review\TrafficArea', $mockTaService);

        // Expectations
        $mockTaService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TACONFIG');

        $mockTotalAuthService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TOTAL_AUTH_CONFIG');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    /**
     * @dataProvider psvProvider
     */
    public function testGetConfigFromDataWithOcList($licenceCat, $expectedOcService, $expectedTaService)
    {
        $data = [
            'goodsOrPsv' => ['id' => $licenceCat],
            'operatingCentres' => [
                ['foo' => 'bar'],
                ['foo' => 'cake']
            ]
        ];
        $expected = [
            'subSections' => [
                [
                    'mainItems' => [
                        'foobar',
                        'foocake'
                    ],
                ],
                [
                    'mainItems' => [
                        'TACONFIG',
                        'TOTAL_AUTH_CONFIG'
                    ]
                ]
            ]
        ];

        // Mocks
        $mockOcService = m::mock();
        $mockTotalAuthService = m::mock();
        $mockTaService = m::mock();
        $this->sm->setService('Review\\' . $expectedOcService, $mockOcService);
        $this->sm->setService('Review\\' . $expectedTaService, $mockTotalAuthService);
        $this->sm->setService('Review\TrafficArea', $mockTaService);

        // Expectations
        $mockTaService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TACONFIG');

        $mockTotalAuthService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('TOTAL_AUTH_CONFIG');

        $mockOcService->shouldReceive('getConfigFromData')
            ->with(['foo' => 'bar'])
            ->andReturn('foobar')
            ->shouldReceive('getConfigFromData')
            ->with(['foo' => 'cake'])
            ->andReturn('foocake');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function psvProvider()
    {
        return [
            [
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                'GoodsOperatingCentre',
                'ApplicationGoodsOcTotalAuth'
            ],
            [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                'PsvOperatingCentre',
                'ApplicationPsvOcTotalAuth'
            ]
        ];
    }
}
