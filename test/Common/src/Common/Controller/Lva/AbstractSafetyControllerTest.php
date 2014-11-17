<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractSafetyControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractSafetyController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\Safety');

        $form->shouldReceive('setData')
            ->with(
                [
                    'licence' => [
                        'safetyInsVehicles' => 'inspection_interval_vehicle.x',
                        'safetyInsTrailers' => 'inspection_interval_trailer.y'
                    ],
                    'application' => [
                        'version' => 1,
                        'safetyConfirmation' => 'N',
                        'isMaintenanceSuitable' => 'N'
                    ]
                ]
            )
            ->andReturn($form)
            ->shouldReceive('get')
            ->with('table')
            ->andReturn(m::mock('\Zend\Form\Fieldset'))
            ->shouldReceive('get');

        $this->sut->shouldReceive('getSafetyData')
            ->andReturn(
                [
                    'version' => 1,
                    'safetyConfirmation' => 'N',
                    'isMaintenanceSuitable' => 'N',
                    'licence' => [
                        'safetyInsVehicles' => 'x',
                        'safetyInsTrailers' => 'y'
                    ]
                ]
            )
            ->shouldReceive('getTypeOfLicenceData')
            ->andReturn(
                [
                    'version' => 1,
                    'niFlag' => 'x',
                    'goodsOrPsv' => 'y',
                    'licenceType' => 'z'
                ]
            )
            ->shouldReceive('getLicenceId')
            ->andReturn(456);

        $this->mockService('Table', 'prepareTable')
            ->with('lva-safety', [])
            ->andReturn(m::mock('\Common\Service\Table\TableBuilder'));

        $this->mockEntity('Workshop', 'getForLicence')
            ->with(456)
            ->andReturn([]);

        $this->getMockFormHelper()
            ->shouldReceive('populateFormTable');

        $this->shouldRemoveElements(
            $form,
            [
                'application->isMaintenanceSuitable'
            ]
        );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('safety', $this->view);
    }
}
