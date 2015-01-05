<?php

/**
 * Application Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Controller\Lva\Adapters\ApplicationTypeOfLicenceAdapter;
use CommonTest\Bootstrap;
use Common\Service\Entity\ApplicationCompletionEntityService;

/**
 * Application Type Of Licence Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTypeOfLicenceAdapterTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sut = new ApplicationTypeOfLicenceAdapter();

        $this->sm = Bootstrap::getServiceManager();
        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testGetQueryParams()
    {
        $this->assertEquals(['query' => []], $this->sut->getQueryParams());
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testGetRouteParams()
    {
        $this->assertEquals(['action' => 'confirmation'], $this->sut->getRouteParams());
    }

    /**
     * @group application_type_of_licence_adapter
     * @dataProvider providerForDoesChangeRequireConfirmation
     */
    public function testDoesChangeRequireConfirmation($postData, $currentData, $expected)
    {
        $this->assertEquals($expected, $this->sut->doesChangeRequireConfirmation($postData, $currentData));
    }

    public function providerForDoesChangeRequireConfirmation()
    {
        return [
            'noCurrentData' => [
                [],
                [
                    'niFlag' => null,
                    'goodsOrPsv' => null,
                    'licenceType' => null
                ],
                false
            ],
            'noChange' => [
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ],
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_sn'
                ],
                false
            ],
            'changedOperatorLocation' => [
                [
                    'operator-location' => 'Y'
                ],
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'xxx',
                    'licenceType' => 'xxx'
                ],
                true
            ],
            'changedOperatorType' => [
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv'
                ],
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_psv',
                    'licenceType' => 'xxx'
                ],
                true
            ],
            'changedLicenceTypeWithoutSR' => [
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sn'
                ],
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_si'
                ],
                false
            ],
            'changedLicenceTypeFromSR' => [
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_sr'
                ],
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_si'
                ],
                true
            ],
            'changedLicenceTypeToSR' => [
                [
                    'operator-location' => 'N',
                    'operator-type' => 'lcat_gv',
                    'licence-type' => 'ltyp_si'
                ],
                [
                    'niFlag' => 'N',
                    'goodsOrPsv' => 'lcat_gv',
                    'licenceType' => 'ltyp_sr'
                ],
                true
            ]
        ];
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testIsCurrentDataSet()
    {
        $data = [
            'niFlag' => null,
            'goodsOrPsv' => null,
            'licenceType' => null
        ];

        $this->assertFalse($this->sut->isCurrentDataSet($data));
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testIsCurrentDataSetWithData()
    {
        $data = [
            'niFlag' => 'xxx',
            'goodsOrPsv' => 'xxx',
            'licenceType' => 'xxx'
        ];

        $this->assertTrue($this->sut->isCurrentDataSet($data));
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testProcessChangeWithoutCurrenetData()
    {
        $postData = [];
        $currentData = [
            'niFlag' => null,
            'goodsOrPsv' => null,
            'licenceType' => null
        ];

        $this->assertFalse($this->sut->processChange($postData, $currentData));
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testProcessChangeWithoutLicenceTypeChange()
    {
        $postData = [
            'licence-type' => 'xxx'
        ];
        $currentData = [
            'niFlag' => 'N',
            'goodsOrPsv' => 'lcat_psv',
            'licenceType' => 'xxx'
        ];

        $this->assertFalse($this->sut->processChange($postData, $currentData));
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testProcessChangeWithLicenceTypeChange()
    {
        $postData = [
            'licence-type' => 'ltyp_sn'
        ];
        $currentData = [
            'niFlag' => 'N',
            'goodsOrPsv' => 'lcat_psv',
            'licenceType' => 'ltyp_si'
        ];

        $expectedUpdateData = [
            'licenceType' => 'ltyp_sn'
        ];

        $stubbedCompletionData = [
            'id' => 2,
            'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
            'fooStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
            'barStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
            'bazStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
        ];

        $expectedCompletionData = [
            'id' => 2,
            'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
            'fooStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
            'barStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
            'bazStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
        ];

        $applicationId = 4;
        $licenceId = 5;

        $this->controller->shouldReceive('params')
            ->with('application')
            ->andReturn($applicationId);

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('forceUpdate')
            ->with($applicationId, $expectedUpdateData)
            ->shouldReceive('getLicenceIdForApplication')
            ->with($applicationId)
            ->andReturn($licenceId);

        $mockProcessingService = m::mock();
        $mockProcessingService->shouldReceive('cancelFees')
            ->with($licenceId)
            ->shouldReceive('createFee')
            ->with($applicationId, $licenceId, 'APP');

        $mockAppCompletionService = m::mock();
        $mockAppCompletionService->shouldReceive('getCompletionStatuses')
            ->with($applicationId)
            ->andReturn($stubbedCompletionData)
            ->shouldReceive('save')
            ->with($expectedCompletionData);

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Processing\Application', $mockProcessingService);
        $this->sm->setService('Entity\ApplicationCompletion', $mockAppCompletionService);

        $this->assertTrue($this->sut->processChange($postData, $currentData));
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testProcessFirstSave()
    {
        $applicationId = 4;

        $this->expectCreateFee($applicationId);

        $this->sut->processFirstSave($applicationId);
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testCreateFee()
    {
        $applicationId = 4;

        $this->expectCreateFee($applicationId);

        $this->sut->createFee($applicationId);
    }

    /**
     * @NOTE both processFirstSave just calls createFee in this adapter, so this prevents duplication
     */
    protected function expectCreateFee($applicationId)
    {
        $licenceId = 6;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with($applicationId)
            ->andReturn($licenceId);

        $mockProcessingService = m::mock();
        $mockProcessingService->shouldReceive('createFee')
            ->with($applicationId, $licenceId, 'APP');

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Processing\Application', $mockProcessingService);
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testConfirmationActionWithoutPost()
    {
        $mockRequest = m::mock();
        $mockRequest->shouldReceive('isPost')
            ->andReturn(false);

        $this->controller->shouldReceive('getRequest')
            ->andReturn($mockRequest);

        $mockFormHelper = m::mock();
        $mockFormHelper->shouldReceive('createForm')
            ->with('GenericConfirmation')
            ->andReturn('FORM')
            ->shouldReceive('setFormActionFromRequest')
            ->with('FORM', $mockRequest);

        $this->sm->setService('Helper\Form', $mockFormHelper);

        $response = $this->sut->confirmationAction();

        $this->assertEquals('FORM', $response);
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testConfirmationActionWithPostWithoutConfirmation()
    {
        $applicationId = 4;
        $stubbedQuery = [
            'operator-type' => 'lcat_gv',
            'operator-location' => 'N',
            'licence-type' => 'ltyp_sn'
        ];
        $stubbedTypeOfLicenceData = [
            'goodsOrPsv' => 'lcat_gv',
            'niFlag' => 'N',
            'licenceType' => 'ltyp_sn'
        ];

        $mockRequest = m::mock();
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $mockParams = m::mock();
        $mockParams->shouldReceive('fromQuery')
            ->andReturn($stubbedQuery);

        $this->controller->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('application')
            ->andReturn($applicationId)
            ->shouldReceive('params')
            ->with()
            ->andReturn($mockParams);

        $this->controller->shouldReceive('redirect->toRoute')
            ->with(null, ['action' => null], [], true)
            ->andReturn('REDIRECT');

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getTypeOfLicenceData')
            ->with($applicationId)
            ->andReturn($stubbedTypeOfLicenceData);

        $mockFlashMessenger = m::mock();
        $mockFlashMessenger->shouldReceive('addWarningMessage')
            ->with('tol-no-changes-message');

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        $response = $this->sut->confirmationAction();

        $this->assertEquals('REDIRECT', $response);
    }

    /**
     * @group application_type_of_licence_adapter
     */
    public function testConfirmationActionWithPostWithConfirmation()
    {
        $applicationId = 4;
        $stubbedQuery = [
            'operator-type' => 'lcat_gv',
            'operator-location' => 'Y',
            'licence-type' => 'ltyp_sn'
        ];
        $stubbedTypeOfLicenceData = [
            'goodsOrPsv' => 'lcat_gv',
            'niFlag' => 'N',
            'licenceType' => 'ltyp_sn'
        ];
        $stubbedOrganisation = [
            'id' => 1
        ];
        $expectedAppData = [
            'niFlag' => 'Y',
            'goodsOrPsv' => 'lcat_gv',
            'licenceType' => 'ltyp_sn'
        ];
        $stubbedAppData = [
            'application' => 8,
            'licence' => 9
        ];

        $mockRequest = m::mock();
        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $mockParams = m::mock();
        $mockParams->shouldReceive('fromQuery')
            ->andReturn($stubbedQuery);

        $this->controller->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('application')
            ->andReturn($applicationId)
            ->shouldReceive('params')
            ->with()
            ->andReturn($mockParams);

        $this->controller->shouldReceive('redirect->toRouteAjax')
            ->with('lva-application', ['application' => 8])
            ->andReturn('REDIRECT');

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getTypeOfLicenceData')
            ->with($applicationId)
            ->andReturn($stubbedTypeOfLicenceData)
            ->shouldReceive('getOrganisation')
            ->with($applicationId)
            ->andReturn($stubbedOrganisation)
            // removeApplication
            ->shouldReceive('delete')
            ->with($applicationId)
            // createApplication
            ->shouldReceive('createNew')
            ->with(1, $expectedAppData)
            ->andReturn($stubbedAppData)
            ->shouldReceive('getLicenceIdForApplication')
            ->with(8)
            ->andReturn(9);

        // removeApplication
        $mockTaskService = m::mock();
        $mockTaskService->shouldReceive('deleteList')
            ->with(['application' => $applicationId]);

        // createApplication
        $mockAppCompletion = m::mock();
        $mockAppCompletion->shouldReceive('updateCompletionStatuses')
            ->with(8, 'type_of_licence');

        $mockProcessingService = m::mock();
        $mockProcessingService->shouldReceive('createFee')
            ->with(8, 9, 'APP');

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Entity\ApplicationCompletion', $mockAppCompletion);
        $this->sm->setService('Entity\Task', $mockTaskService);
        $this->sm->setService('Processing\Application', $mockProcessingService);

        $response = $this->sut->confirmationAction();

        $this->assertEquals('REDIRECT', $response);
    }
}
