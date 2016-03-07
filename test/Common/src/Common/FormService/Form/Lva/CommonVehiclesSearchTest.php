<?php

/**
 * Common Vehicles Search Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\CommonVehiclesSearch;

/**
 * Common Vehicles Search Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommonVehiclesSearchTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');

        $this->sut = new CommonVehiclesSearch();
        $this->sut->setFormHelper($this->formHelper);
    }

    public function testGetForm()
    {
        $mockForm = m::mock();

        $this->formHelper->shouldReceive('createForm')
            ->with('Lva\VehicleSearch', false)
            ->andReturn($mockForm);

        $form = $this->sut->getForm();

        $this->assertSame($mockForm, $form);
    }
}
