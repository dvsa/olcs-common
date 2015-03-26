<?php

/**
 * Variation Vehicles Declarations Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\VariationVehiclesDeclarationsReviewService;

/**
 * Variation Vehicles Declarations Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesDeclarationsReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new VariationVehiclesDeclarationsReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetConfigFromData()
    {
        $data = [
            'foo' => 'bar'
        ];

        $mockApplicationService = m::mock();
        $this->sm->setService('Review\ApplicationVehiclesDeclarations', $mockApplicationService);
        $mockApplicationService->shouldReceive('getConfigFromData')
            ->with($data)
            ->andReturn('CONFIG');

        $this->assertEquals('CONFIG', $this->sut->getConfigFromData($data));
    }
}
