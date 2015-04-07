<?php

/**
 * Licence Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\LicencePsvVehicles;

/**
 * Licence Psv Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicencePsvVehiclesTest extends MockeryTestCase
{
    protected $sut;

    protected $bsm;

    public function setUp()
    {
        $this->sut = new LicencePsvVehicles();

        $this->bsm = m::mock('\Common\BusinessService\BusinessServiceManager')->makePartial();

        $this->sut->setBusinessServiceManager($this->bsm);
    }

    public function testProcess()
    {
        $data = ['foo' => 'bar'];

        $mockLicence = m::mock('\Common\BusinessService\BusinessServiceInterface');
        $this->bsm->setService('Lva\Licence', $mockLicence);

        $mockLicence->shouldReceive('process')
            ->with($data)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->process($data));
    }
}
