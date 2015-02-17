<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Vehicles Declarations Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractVehiclesDeclarationsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractVehiclesDeclarationsController');

        $this->mockService('Script', 'loadFile')->with('vehicle-declarations');

        $this->mockService('Helper\Translation', 'translate')
            ->andReturnUsing(
                function ($input) {
                    return $input;
                }
            );
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
                        'psvSmallVhlUndertakings' =>
                            'application_vehicle-safety_undertakings.smallVehiclesUndertakings.text',
                        'psvSmallVhlScotland' =>
                            'application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.text'
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
            )
            ->andReturn($form);

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
                    'totAuthSmallVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null
                ]
            );

        $this->shouldRemoveElements(
            $form,
            [
                'smallVehiclesIntention',
                'nineOrMore',
                'limousinesNoveltyVehicles->psvOnlyLimousinesConfirmationLabel',
                'limousinesNoveltyVehicles->psvOnlyLimousinesConfirmation'
            ]
        );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_declarations', $this->view);
    }
}
