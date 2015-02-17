<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Safety Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractSafetyControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractSafetyController');

        $this->mockService('Script', 'loadFiles')->with(['vehicle-safety', 'lva-crud']);
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

    public function testBasicAddAction()
    {
        $form = $this->createMockForm('Lva\SafetyProviders');

        $form->shouldReceive('setData')
            ->andReturn($form);

        $this->getMockFormHelper()
            ->shouldReceive('processAddressLookupForm')
            ->with($form, $this->request);

        $this->mockRender();

        $this->sut->shouldReceive('params')
            ->with('child_id')
            ->andReturn(50);

        $this->sut->addAction();

        $this->assertEquals('add_safety', $this->view);
    }
}
