<?php

/**
 * Variation Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Review\VariationOperatingCentresReviewService;

/**
 * Variation Operating Centres Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentresReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationOperatingCentresReviewService();
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
                    'title' => 'variation-review-operating-centres-ta-auth-title',
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
                [
                    'action' => 'A',
                    'foo' => 'bar'
                ],
                [
                    'action' => 'A',
                    'foo' => 'bar1'
                ],
                [
                    'action' => 'U',
                    'foo' => 'cake'
                ],
                [
                    'action' => 'D',
                    'foo' => 'blah'
                ]
            ]
        ];
        $expected = [
            'subSections' => [
                [
                    'title' => 'variation-review-operating-centres-added-title',
                    'mainItems' => [
                        'foobar',
                        'foobar1'
                    ],
                ],
                [
                    'title' => 'variation-review-operating-centres-updated-title',
                    'mainItems' => [
                        'foocake'
                    ],
                ],
                [
                    'title' => 'variation-review-operating-centres-deleted-title',
                    'mainItems' => [
                        'fooblah'
                    ],
                ],
                [
                    'title' => 'variation-review-operating-centres-ta-auth-title',
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
            ->with(['action' => 'A', 'foo' => 'bar'])
            ->andReturn('foobar')
            ->shouldReceive('getConfigFromData')
            ->with(['action' => 'A', 'foo' => 'bar1'])
            ->andReturn('foobar1')
            ->shouldReceive('getConfigFromData')
            ->with(['action' => 'U', 'foo' => 'cake'])
            ->andReturn('foocake')
            ->shouldReceive('getConfigFromData')
            ->with(['action' => 'D', 'foo' => 'blah'])
            ->andReturn('fooblah');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function psvProvider()
    {
        return [
            [
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                'GoodsOperatingCentre',
                'VariationGoodsOcTotalAuth'
            ],
            [
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                'PsvOperatingCentre',
                'VariationPsvOcTotalAuth'
            ]
        ];
    }
}
