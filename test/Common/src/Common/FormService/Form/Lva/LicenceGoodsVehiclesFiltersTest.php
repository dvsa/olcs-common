<?php

namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\LicenceGoodsVehiclesFilters;

/**
 * @covers \Common\FormService\Form\Lva\LicenceGoodsVehiclesFilters
 */
class LicenceGoodsVehiclesFiltersTest extends MockeryTestCase
{
    /** @var  LicenceGoodsVehiclesFilters */
    protected $sut;
    /** @var  \Common\Service\Helper\FormHelperService | m\MockInterface */
    protected $formHelper;

    public function setUp(): void
    {
        $this->formHelper = m::mock(\Common\Service\Helper\FormHelperService::class);

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
