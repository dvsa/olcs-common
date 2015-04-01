<?php

/**
 * Variation Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\VariationPsvVehicles;

/**
 * Variation Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPsvVehiclesTest extends MockeryTestCase
{
    protected $sut;

    protected $bsm;

    public function setUp()
    {
        $this->sut = new VariationPsvVehicles();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcess()
    {
        $data = ['foo' => 'bar'];

        $mockVariation = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\Application', $mockVariation);

        $mockVariation->shouldReceive('process')
            ->with($data)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->process($data));
    }
}
