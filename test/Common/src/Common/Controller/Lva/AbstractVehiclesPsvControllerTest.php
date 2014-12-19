<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

/**
 * Test Abstract Vehicles PSV Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractVehiclesPsvControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractVehiclesPsvController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\PsvVehicles');

        $form->shouldReceive('setData')
            ->with(
                [
                    'data' => [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                ]
            )
            ->andReturn($form)
            ->shouldReceive('has')
            ->with('small')
            ->andReturn(false)
            ->shouldReceive('has')
            ->with('medium')
            ->andReturn(false)
            ->shouldReceive('has')
            ->with('large')
            ->andReturn(false);

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(321)
            ->shouldReceive('getLvaEntityService')
            ->andReturn(
                m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                ->with(321)
                ->andReturn(
                    [
                        'version' => 1,
                        'hasEnteredReg' => 'N'
                    ]
                )
                ->getMock()
            )
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_psv', $this->view);
    }

    public function testBasicSmallAddAction()
    {
        $form = $this->createMockForm('Lva\PsvVehiclesVehicle');

        $specifiedDate = m::mock();
        $removalDate = m::mock();

        $form->shouldReceive('setData')
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('discNo')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setAttribute')
                    ->with('disabled', 'disabled')
                    ->getMock()
                )
                ->shouldReceive('get')
                ->with('specifiedDate')
                ->andReturn($specifiedDate)
                ->shouldReceive('get')
                ->with('removalDate')
                ->andReturn($removalDate)
                ->shouldReceive('has')
                ->with('receivedDate')
                ->andReturn(false)
                ->getMock()
            );

        $this->getMockFormHelper()
            ->shouldReceive('disableDateElement')
            ->with($specifiedDate)
            ->shouldReceive('disableDateElement')
            ->with($removalDate);

        $this->shouldRemoveElements(
            $form,
            [
                'data->isNovelty',
                'data->makeModel',
                'licence-vehicle->discNo'
            ]
        );

        $this->mockRender();

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(50)
            ->shouldReceive('params')
            ->with('action')
            ->andReturn('small-add');

        $this->mockEntity('LicenceVehicle', 'getVehiclePsv')
            ->with(50)
            ->andReturn([]);

        $this->sut->smallAddAction();

        $this->assertEquals('add_vehicle', $this->view);
    }
}
