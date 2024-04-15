<?php

namespace CommonTest\Common\Controller\Lva;

use Common\Controller\Lva\AbstractVehiclesDeclarationsController;
use Common\FormService\FormServiceManager;
use Common\RefData;
use Common\Service\Helper\DataHelperService;
use Common\Service\Helper\FormHelperService;
use Common\Service\Script\ScriptFactory;
use Common\View\Model\Section;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Mockery as m;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Test Abstract Vehicles Declarations Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractVehiclesDeclarationsControllerTest extends AbstractLvaControllerTestCase
{
    /**
     * @var \Mockery\LegacyMockInterface
     */
    public $mockNiTextTranslationUtil;
    /**
     * @var \Mockery\LegacyMockInterface
     */
    public $mockAuthService;
    public $mockScriptFactory;
    public $mockFormServiceManager;
    public $mockFormHelper;
    /**
     * @var \Mockery\LegacyMockInterface
     */
    public $mockDataHelper;
    public $view;
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockNiTextTranslationUtil = m::mock(NiTextTranslation::class);
        $this->mockAuthService = m::mock(AuthorizationService::class);
        $this->mockScriptFactory = m::mock(ScriptFactory::class);
        $this->mockFormServiceManager = m::mock(FormServiceManager::class);
        $this->mockFormHelper = m::mock(FormHelperService::class);
        $this->mockDataHelper = m::mock(DataHelperService::class);

        $this->sut = m::mock(AbstractVehiclesDeclarationsController::class, [
            $this->mockNiTextTranslationUtil,
            $this->mockAuthService,
            $this->mockFormHelper,
            $this->mockFormServiceManager,
            $this->mockScriptFactory,
            $this->mockDataHelper
        ])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockScriptFactory->shouldReceive('loadFile')->with('vehicle-declarations');
    }

    public function testGetIndexAction(): void
    {
        $form = m::mock(\Common\Form\Form::class);
        $mockFormService = m::mock();

        $form->shouldReceive('setValidationGroup')->once();

        $this->mockFormServiceManager->shouldReceive('get')
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
                    'psvVehicleSize' => [
                        'size' => 'PSV_SIZE',
                    ],
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

    protected function shouldRemoveElements(\Common\Form\Form $form, array $elements): void
    {
        $helper = $this->mockFormHelper;
        foreach ($elements as $e) {
            $helper->shouldReceive('remove')
                ->with($form, $e)
                ->andReturn($helper);
        }
    }

    protected function mockRender()
    {
        $this->sut->shouldReceive('render')
            ->once()
            ->andReturnUsing(
                function ($view, $form = null) {

                    /**
                     * assign the view variable so we can interrogate it later
                     */
                    $this->view = $view;

                    /*
                     * but also return it, since that's a closer simulation
                     * of what 'render' would normally do
                     */

                    return new Section();
                }
            );

        return $this->sut;
    }
}
