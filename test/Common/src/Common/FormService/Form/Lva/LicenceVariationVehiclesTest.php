<?php

namespace CommonTest\Common\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\LicenceVariationVehicles;

/**
 * Licence Variation Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVariationVehiclesTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    public function setUp(): void
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');

        $this->sut = new LicenceVariationVehicles($this->formHelper);
    }

    public function testAlterForm()
    {
        $mockForm = m::mock();

        $this->formHelper->shouldReceive('remove')
            ->once()
            ->with($mockForm, 'data->hasEnteredReg')
            ->shouldReceive('remove')
            ->once()
            ->with($mockForm, 'data->notice');

        $this->sut->alterForm($mockForm);
    }
}
