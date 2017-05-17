<?php

/**
 * Licence Goods Vehicles Filters Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\LicenceGoodsVehiclesFilters;

/**
 * Licence Goods Vehicles Filters Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesFiltersTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');

        $this->sut = new LicenceGoodsVehiclesFilters();
        $this->sut->setFormHelper($this->formHelper);
    }

    public function testGetForm()
    {
        // Mocks
        $mockForm = m::mock();

        // Expectations
        $this->formHelper->shouldReceive('createForm')
            ->with('Lva\VehicleFilter', false)
            ->andReturn($mockForm);

        $options = [
            'All' => 'All',
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
            'E' => 'E',
            'F' => 'F',
            'G' => 'G',
            'H' => 'H',
            'I' => 'I',
            'J' => 'J',
            'K' => 'K',
            'L' => 'L',
            'M' => 'M',
            'N' => 'N',
            'O' => 'O',
            'P' => 'P',
            'Q' => 'Q',
            'R' => 'R',
            'S' => 'S',
            'T' => 'T',
            'U' => 'U',
            'V' => 'V',
            'W' => 'W',
            'X' => 'X',
            'Y' => 'Y',
            'Z' => 'Z',
        ];

        $mockForm->shouldReceive('get')
            ->with('vrm')
            ->andReturn(
                m::mock()
                ->shouldReceive('setValueOptions')
                ->with($options)
                ->getMock()
            );

        // <<-- START SUT::getForm
        $this->formHelper->shouldReceive('remove')
            ->with($mockForm, 'specified');
        // <<-- END SUT::getForm

        $form = $this->sut->getForm();

        $this->assertSame($mockForm, $form);
    }
}
