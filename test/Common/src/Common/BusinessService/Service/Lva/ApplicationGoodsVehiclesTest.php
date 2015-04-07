<?php

/**
 * Application Goods Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\ApplicationGoodsVehicles;

/**
 * Applications Goods Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehiclesTest extends MockeryTestCase
{
    protected $sut;

    protected $bsm;

    public function setUp()
    {
        $this->sut = new ApplicationGoodsVehicles();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcess()
    {
        $data = ['foo' => 'bar'];

        $mockApplication = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\Application', $mockApplication);

        $mockApplication->shouldReceive('process')
            ->with($data)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->process($data));
    }
}
