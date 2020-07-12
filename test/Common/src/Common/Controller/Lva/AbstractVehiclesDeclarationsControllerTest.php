<?php

namespace CommonTest\Controller\Lva;

use Common\RefData;
use Mockery as m;
use CommonTest\Bootstrap;

/**
 * Test Abstract Vehicles Declarations Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractVehiclesDeclarationsControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp(): void
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

        $mockFormServiceManager = m::mock();
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);
    }

    public function testGetIndexAction()
    {
        $form = m::mock(\Common\Form\Form::class);
        $mockFormService = m::mock();
        $mockFormServiceManager = m::mock();
        $this->sm->setService('FormServiceManager', $mockFormServiceManager);

        $form->shouldReceive('setValidationGroup')->once();

        $mockFormServiceManager->shouldReceive('get')
            ->once()
            ->with('lva--vehicles_declarations')
            ->andReturn($mockFormService);

        $mockFormService
            ->shouldReceive('getForm')
            ->once()
            ->andReturn($form);

        $form->shouldReceive('setData')
            ->with(
                [
                    'version' => 1,
                    'psvVehicleSize' => array(
                        'size' => 'PSV_SIZE',
                    ),
                    'smallVehiclesIntention' => [
                        'psvOperateSmallVhl' => 'x',
                        'psvSmallVhlNotes' => '',
                        'psvSmallVhlConfirmation' => 'y',
                    ],
                    'nineOrMore' => [
                        'psvNoSmallVhlConfirmation' => 'y',
                    ],
                    'mainOccupation' => [
                        'psvMediumVhlConfirmation' => null,
                        'psvMediumVhlNotes' => null
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

        $this->sut->shouldReceive('handleQuery')->once()->andReturn(
            m::mock()->shouldReceive('isOk')->andReturn(true)->getMock()->shouldReceive('getResult')->andReturn(
                [
                    'version' => 1,
                    'psvOperateSmallVhl' => 'x',
                    'psvSmallVhlNotes' => '',
                    'psvSmallVhlConfirmation' => 'y',
                    'psvNoSmallVhlConfirmation' => 'y',
                    'psvLimousines' => '',
                    'psvNoLimousineConfirmation' => '',
                    'psvOnlyLimousinesConfirmation' => '',
                    'psvWhichVehicleSizes' => ['id' => 'PSV_SIZE'],
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'psvMediumVhlConfirmation' => null,
                    'psvMediumVhlNotes' => null,
                    'licenceType' => [
                        'id' => RefData::LICENCE_TYPE_STANDARD_NATIONAL
                    ]
                ]
            )->getMock()
        );

        $this->shouldRemoveElements(
            $form,
            [
                'smallVehiclesIntention',
                'nineOrMore',
                'mainOccupation',
                'limousinesNoveltyVehicles->psvOnlyLimousinesConfirmationLabel',
                'limousinesNoveltyVehicles->psvOnlyLimousinesConfirmation'
            ]
        );

        $this->mockRender();

        $this->sut->indexAction();

        $this->assertEquals('vehicles_declarations', $this->view);
    }
}
