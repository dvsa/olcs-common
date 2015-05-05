<?php

/**
 * Variation Transport Managers Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Review\VariationTransportManagersReviewService;

/**
 * Variation Transport Managers Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTransportManagersReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationTransportManagersReviewService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromDataOneOfEach()
    {
        $tm1 = [
            'action' => 'A',
            'foo' => 'A'
        ];

        $tm2 = [
            'action' => 'U',
            'foo' => 'U'
        ];

        $tm3 = [
            'action' => 'D',
            'foo' => 'D'
        ];

        $data = [
            'transportManagers' => [
                $tm1, $tm2, $tm3
            ]
        ];

        $expected = [
            'subSections' => [
                [
                    'title' => 'review-transport-manager-added-title',
                    'mainItems' => ['foo' => 'bar']
                ],
                [
                    'title' => 'review-transport-manager-updated-title',
                    'mainItems' => ['foo' => 'bar']
                ],
                [
                    'title' => 'review-transport-manager-deleted-title',
                    'mainItems' => ['foo' => 'bar']
                ]
            ]
        ];

        $mockTm = m::mock();
        $this->sm->setService('Review\TransportManagers', $mockTm);

        $mockTm->shouldReceive('getConfigFromData')
            ->once()
            ->with([$tm1])
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('getConfigFromData')
            ->once()
            ->with([$tm2])
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('getConfigFromData')
            ->once()
            ->with([$tm3])
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function testGetConfigFromDataMultipleAndNone()
    {
        $tm1 = [
            'action' => 'A',
            'foo' => 'A'
        ];

        $tm2 = [
            'action' => 'A',
            'foo' => 'A'
        ];

        $tm3 = [
            'action' => 'D',
            'foo' => 'D'
        ];

        $data = [
            'transportManagers' => [
                $tm1, $tm2, $tm3
            ]
        ];

        $expected = [
            'subSections' => [
                [
                    'title' => 'review-transport-manager-added-title',
                    'mainItems' => ['foo' => 'bar']
                ],
                [
                    'title' => 'review-transport-manager-deleted-title',
                    'mainItems' => ['foo' => 'bar']
                ]
            ]
        ];

        $mockTm = m::mock();
        $this->sm->setService('Review\TransportManagers', $mockTm);

        $mockTm->shouldReceive('getConfigFromData')
            ->once()
            ->with([$tm1, $tm2])
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('getConfigFromData')
            ->once()
            ->with([$tm3])
            ->andReturn(['foo' => 'bar']);

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }
}
