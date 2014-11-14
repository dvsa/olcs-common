<?php

namespace CommonTest\Controller\Lva;

use \Mockery as m;

class AbstractVehiclesDeclarationsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractVehiclesDeclarationsController');
    }

    public function testGetIndexAction()
    {
        $form = $this->createMockForm('Lva\VehiclesDeclarations');

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'smallVehiclesIntention' => [
                        'psvOperateSmallVhl' => 'x',
                        'psvSmallVhlNotes' => '',
                        'psvSmallVhlConfirmation' => 'y',
                        'psvSmallVhlUndertakings' => 'application_vehicle-safety_undertakings.smallVehiclesUndertakings.text',
                        'psvSmallVhlScotland' => 'application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.text'
                    ],
                    'nineOrMore' => [
                        'psvNoSmallVhlConfirmation' => 'y',
                    ],
                    'limousinesNoveltyVehicles' => [
                        'psvLimousines' => '',
                        'psvNoLimousineConfirmation' => '',
                        'psvOnlyLimousinesConfirmation' => '',
                    ]
                ]
            );

        $this->sut->shouldReceive('getApplicationId')
            ->andReturn(13);

        $this->mockEntity('Application', 'getDataForVehiclesDeclarations')
            ->with(13)
            ->andReturn(
                [
                    'version' => 1,
                    'psvOperateSmallVhl' => 'x',
                    'psvSmallVhlNotes' => '',
                    'psvSmallVhlConfirmation' => 'y',
                    'psvNoSmallVhlConfirmation' => 'y',
                    'psvLimousines' => '',
                    'psvNoLimousineConfirmation' => '',
                    'psvOnlyLimousinesConfirmation' => '',
                ]
            );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_declarations', $this->view);
    }
}
