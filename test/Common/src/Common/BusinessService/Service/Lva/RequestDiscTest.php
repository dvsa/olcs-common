<?php

/**
 * Request Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\Service\Lva\RequestDisc;
use Common\BusinessService\Response;

/**
 * Request Disc Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RequestDiscTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new RequestDisc();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcess()
    {
        $params = [
            'foo' => 'bar',
            'isCopy' => 'N',
            'licenceVehicle' => 111
        ];

        // Mocks
        $mockGoodsDisc = m::mock();
        $this->sm->setService('Entity\GoodsDisc', $mockGoodsDisc);

        // Expectations
        $mockGoodsDisc->shouldReceive('save')
            ->with(['isCopy' => 'N', 'licenceVehicle' => 111]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
    }
}
