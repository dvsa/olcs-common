<?php

/**
 * Psv Variation Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Foo\Bar\Cake;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Controller\Lva\Traits\Stubs\PsvVariationControllerTraitStub;

/**
 * Psv Variation Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVariationControllerTraitTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new PsvVariationControllerTraitStub();
    }

    public function testShowVehicle()
    {
        $licenceVehicle = [
            'removalDate' => '2014-01-01'
        ];

        $this->assertFalse($this->sut->callShowVehicle($licenceVehicle));
    }

    public function testShowVehicleTrue()
    {
        $licenceVehicle = [
            'removalDate' => null
        ];

        $this->assertTrue($this->sut->callShowVehicle($licenceVehicle));
    }
}
