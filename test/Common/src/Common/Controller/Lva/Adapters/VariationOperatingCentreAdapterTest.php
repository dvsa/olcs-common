<?php

/**
 * Variation Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationOperatingCentreAdapter;
use Common\Service\Entity\LicenceEntityService;
use CommonTest\Bootstrap;

/**
 * Variation Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentreAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    /**
     * Renamed this method as we are using MUnit for some methods in this file
     */
    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut = new VariationOperatingCentreAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testSaveMainFormData()
    {
        $data = ['dataTrafficArea' => 'A', 'foo' => 'bar'];

        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);

        $mockLvaEntityService = m::mock();
        $this->sm->setService('Entity\Application', $mockLvaEntityService);

        $stubbedData = [
            'foo' => 'bar',
            'trafficArea' => 'A'
        ];

        $mockLicenceService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockLicenceAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceAdapter);

        $mockDataHelper->shouldReceive('processDataMap')
            ->with(['foo' => 'bar'], ['main' => ['mapFrom' => ['data', 'dataTrafficArea']]])
            ->andReturn($stubbedData);

        $mockLicenceAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn(5);

        $mockLicenceService->shouldReceive('setTrafficArea')
            ->with(5, 'A');

        $mockLvaEntityService->shouldReceive('save')
            ->with($stubbedData)
            ->andReturn(1);

        $this->sut->saveMainFormData($data);
    }

    public function testSaveMainFormDataWithoutTa()
    {
        $data = ['dataTrafficArea' => 'A', 'foo' => 'bar'];

        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);

        $mockLvaEntityService = m::mock();
        $this->sm->setService('Entity\Application', $mockLvaEntityService);

        $stubbedData = [
            'foo' => 'bar'
        ];

        $mockDataHelper->shouldReceive('processDataMap')
            ->with(['foo' => 'bar'], ['main' => ['mapFrom' => ['data', 'dataTrafficArea']]])
            ->andReturn($stubbedData);

        $mockLvaEntityService->shouldReceive('save')
            ->with($stubbedData)
            ->andReturn(1);

        $this->sut->saveMainFormData($data);
    }

    public function testAttachMainScripts()
    {
        $mockScript = m::mock();
        $mockScript->shouldReceive('loadFile')
            ->with('lva-crud-delta');

        $this->sm->setService('Script', $mockScript);

        $this->sut->attachMainScripts();
    }

    public function testGetAddressDataWithApplicationId()
    {
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockEntityService);

        $mockEntityService->shouldReceive('getAddressData')
            ->with(1)
            ->andReturn('DATA');

        $this->assertEquals('DATA', $this->sut->getAddressData('A1'));
    }

    public function testGetAddressDataWithLicenceId()
    {
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockEntityService);

        $mockEntityService->shouldReceive('getAddressData')
            ->with(1)
            ->andReturn('DATA');

        $this->assertEquals('DATA', $this->sut->getAddressData('L1'));
    }

    public function testProcessUndeletableResponse()
    {
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        $mockFlashMessenger->shouldreceive('addErrorMessage')
            ->with('could-not-remove-message');

        $this->controller->shouldReceive('redirect->toRouteAjax')
            ->with(null, ['child_id' => null], [], true)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->processUndeletableResponse());
    }

    public function testGetChildId()
    {
        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn('A5');

        $this->assertEquals(5, $this->sut->getChildId());
    }

    public function testSaveActionFormDataForAddWithFailedOcSave()
    {
        $this->setExpectedException('\Exception', 'Unable to save operating centre');

        // Params
        $id = 3;
        $mode = 'add';
        $fileListData = ['foo' => 'bar'];
        $formData = ['form' => 'data'];
        $data = [
            'advertisements' => [
                'file' => [
                    'list' => $fileListData
                ]
            ]
        ];

        // StubbedData
        $formattedData = [
            'applicationOperatingCentre' => [

            ],
            'operatingCentre' => [
                'name' => 'foo'
            ]
        ];
        $actionDataMap = array(
            '_addresses' => array(
                'address'
            ),
            'main' => array(
                'children' => array(
                    'applicationOperatingCentre' => array(
                        'mapFrom' => array(
                            'data',
                            'advertisements'
                        )
                    ),
                    'operatingCentre' => array(
                        'mapFrom' => array(
                            'operatingCentre'
                        ),
                        'children' => array(
                            'addresses' => array(
                                'mapFrom' => array(
                                    'addresses'
                                )
                            )
                        )
                    )
                )
            )
        );

        // Mocks
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);
        $mockLvaEntity = m::mock();
        $this->sm->setService('VariationLvaAdapter', $mockLvaEntity);
        $mockOcEntity = m::mock();
        $this->sm->setService('Entity\OperatingCentre', $mockOcEntity);

        // Expectations
        $mockDataHelper->shouldReceive('processDataMap')
            ->with($formData, $actionDataMap)
            ->andReturn($formattedData);

        $mockLvaEntity->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($id);

        $mockOcEntity->shouldReceive('save')
            ->with(['name' => 'foo']);

        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn(null);

        $this->sut->saveActionFormData($mode, $data, $formData);
    }

    public function testSaveActionFormDataForAddWithFailedAocSave()
    {
        $this->setExpectedException('\Exception', 'Unable to save operating centre');

        // Params
        $id = 3;
        $mode = 'add';
        $fileListData = [
            [
                'id' => 1,
                'version' => 3
            ]
        ];
        $formData = ['form' => 'data'];
        $data = [
            'advertisements' => [
                'file' => [
                    'list' => $fileListData
                ]
            ]
        ];

        // StubbedData
        $formattedData = [
            'applicationOperatingCentre' => [

            ],
            'operatingCentre' => [
                'name' => 'foo'
            ]
        ];
        $actionDataMap = array(
            '_addresses' => array(
                'address'
            ),
            'main' => array(
                'children' => array(
                    'applicationOperatingCentre' => array(
                        'mapFrom' => array(
                            'data',
                            'advertisements'
                        )
                    ),
                    'operatingCentre' => array(
                        'mapFrom' => array(
                            'operatingCentre'
                        ),
                        'children' => array(
                            'addresses' => array(
                                'mapFrom' => array(
                                    'addresses'
                                )
                            )
                        )
                    )
                )
            )
        );
        $stubbedTolData = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV
        ];

        // Mocks
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);
        $mockLvaEntity = m::mock();
        $this->sm->setService('VariationLvaAdapter', $mockLvaEntity);
        $mockOcEntity = m::mock();
        $this->sm->setService('Entity\OperatingCentre', $mockOcEntity);
        $mockDocumentEntity = m::mock();
        $this->sm->setService('Entity\Document', $mockDocumentEntity);
        $mockApplicationService = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationService);
        $mockApplicationOcService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockApplicationOcService);

        // Expectations
        $mockDataHelper->shouldReceive('processDataMap')
            ->with($formData, $actionDataMap)
            ->andReturn($formattedData);

        $mockLvaEntity->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($id);

        $mockOcEntity->shouldReceive('save')
            ->with(['name' => 'foo'])
            ->andReturn(['id' => 5]);

        $mockDocumentEntity->shouldReceive('save')
            ->with(['id' => 1, 'version' => 3, 'operatingCentre' => 5]);

        $mockApplicationService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedTolData);

        $mockApplicationOcService->shouldReceive('save')
            ->with(
                [
                    'operatingCentre' => 5,
                    'action' => 'A',
                    'adPlaced' => 0,
                    'application' => 3
                ]
            );

        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn(null);

        $this->sut->saveActionFormData($mode, $data, $formData);
    }

    public function testSaveActionFormDataForAdd()
    {
        // Params
        $id = 3;
        $mode = 'add';
        $fileListData = [
            [
                'id' => 1,
                'version' => 3
            ]
        ];
        $formData = ['form' => 'data'];
        $data = [
            'advertisements' => [
                'file' => [
                    'list' => $fileListData
                ]
            ]
        ];

        // StubbedData
        $formattedData = [
            'trafficArea' => [
                'id' => 'A'
            ],
            'applicationOperatingCentre' => [

            ],
            'operatingCentre' => [
                'name' => 'foo'
            ]
        ];
        $actionDataMap = array(
            '_addresses' => array(
                'address'
            ),
            'main' => array(
                'children' => array(
                    'applicationOperatingCentre' => array(
                        'mapFrom' => array(
                            'data',
                            'advertisements'
                        )
                    ),
                    'operatingCentre' => array(
                        'mapFrom' => array(
                            'operatingCentre'
                        ),
                        'children' => array(
                            'addresses' => array(
                                'mapFrom' => array(
                                    'addresses'
                                )
                            )
                        )
                    )
                )
            )
        );
        $stubbedTolData = [
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV
        ];

        // Mocks
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);
        $mockLvaEntity = m::mock();
        $this->sm->setService('VariationLvaAdapter', $mockLvaEntity);
        $mockOcEntity = m::mock();
        $this->sm->setService('Entity\OperatingCentre', $mockOcEntity);
        $mockDocumentEntity = m::mock();
        $this->sm->setService('Entity\Document', $mockDocumentEntity);
        $mockApplicationService = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationService);
        $mockApplicationOcService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockApplicationOcService);

        // Expectations
        $mockDataHelper->shouldReceive('processDataMap')
            ->with($formData, $actionDataMap)
            ->andReturn($formattedData);

        $mockLvaEntity->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($id);

        $mockOcEntity->shouldReceive('save')
            ->with(['name' => 'foo'])
            ->andReturn(['id' => 5]);

        $mockDocumentEntity->shouldReceive('save')
            ->with(['id' => 1, 'version' => 3, 'operatingCentre' => 5]);

        $mockApplicationService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedTolData);

        $mockApplicationOcService->shouldReceive('save')
            ->with(
                [
                    'operatingCentre' => 5,
                    'action' => 'A',
                    'adPlaced' => 0,
                    'application' => 3
                ]
            )
            ->andReturn(['id' => 123]);

        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn(null);

        $this->sut->saveActionFormData($mode, $data, $formData);
    }

    public function testAlterForm()
    {
        // Stubbed data
        $id = 3;
        $licenceId = 6;
        $stubbedTolData = [
            'niFlag' => 'Y',
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
        ];
        $stubbedAddressData = [
            'Results' => []
        ];
        $stubbedLicenceAddressData = [
            'Results' => []
        ];
        $stubbedAuths = [
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 5
        ];
        $stubbedLicData = [
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 5
        ];

        // Going to use a real form here to component test this code, as UNIT testing it will be expensive
        $sm = Bootstrap::getRealServiceManager();
        $form = $sm->get('Helper\Form')->createForm('Lva\OperatingCentres');
        // As it's a component test, we will be better off not mocking the form helper
        $this->sm->setService('Helper\Form', $sm->get('Helper\Form'));

        // Mocked services
        $mockVariationLvaAdapter = m::mock();
        $this->sm->setService('variationLvaAdapter', $mockVariationLvaAdapter);
        $mockLicenceLvaAdapter = m::mock();
        $this->sm->setService('licenceLvaAdapter', $mockLicenceLvaAdapter);
        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockAocEntity = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockAocEntity);
        $mockLocEntity = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLocEntity);
        $mockValidator = m::mock('Zend\Validator\ValidatorInterface');
        $this->sm->setService('CantIncreaseValidator', $mockValidator);
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        // Expectations
        $mockVariationLvaAdapter
            ->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('alterForm')
            ->with($form)
            ->shouldReceive('getIdentifier')
            ->andReturn($id);

        $mockLicenceLvaAdapter
            ->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockApplicationEntity->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedTolData)
            ->shouldReceive('getTotalAuths')
            ->andReturn($stubbedAuths);

        $mockAocEntity->shouldReceive('getAddressSummaryData')
            ->with($id)
            ->andReturn($stubbedAddressData);

        $mockLocEntity->shouldReceive('getAddressSummaryData')
            ->with($licenceId)
            ->andReturn($stubbedLicenceAddressData);

        $this->controller->shouldReceive('url->fromRoute')
            ->with('create_variation', ['licence' => $id])
            ->andReturn('URL');

        $mockTranslator->shouldReceive('translateReplace')
            ->with('cant-increase-total-vehicles', ['URL'])
            ->andReturn('MESSAGE 1')
            ->shouldReceive('translateReplace')
            ->with('cant-increase-total-trailers', ['URL'])
            ->andReturn('MESSAGE 2');

        $mockValidator->shouldReceive('setGenericMessage')
            ->with('MESSAGE 1')
            ->shouldReceive('setPreviousValue')
            ->with(10)
            ->shouldReceive('setGenericMessage')
            ->with('MESSAGE 2')
            ->shouldReceive('setPreviousValue')
            ->with(5);

        $mockLicenceEntity->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn($stubbedLicData);

        $mockTranslator->shouldReceive('translateReplace')
            ->with('current-authorisation-hint', [10])
            ->andReturn('HINT 10')
            ->shouldReceive('translateReplace')
            ->with('current-authorisation-hint', [5])
            ->andReturn('HINT 5');

        $alteredForm = $this->sut->alterForm($form);

        $this->assertFalse($alteredForm->get('data')->has('totCommunityLicences'));
    }

    public function testAlterFormWithCommunityLicences()
    {
        // Stubbed data
        $id = 3;
        $licenceId = 6;
        $stubbedTolData = [
            'niFlag' => 'Y',
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
        ];
        $stubbedAddressData = [
            'Results' => []
        ];
        $stubbedLicenceAddressData = [
            'Results' => []
        ];
        $stubbedAuths = [
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 5
        ];
        $stubbedLicData = [
            'totAuthVehicles' => 10,
            'totAuthTrailers' => 5
        ];

        // Going to use a real form here to component test this code, as UNIT testing it will be expensive
        $sm = Bootstrap::getRealServiceManager();
        $form = $sm->get('Helper\Form')->createForm('Lva\OperatingCentres');
        // As it's a component test, we will be better off not mocking the form helper
        $this->sm->setService('Helper\Form', $sm->get('Helper\Form'));
        $sm->setAllowOverride(true);
        $mockViewRenderer = m::mock();
        $sm->setService('ViewRenderer', $mockViewRenderer);

        // Mocked services
        $mockVariationLvaAdapter = m::mock();
        $this->sm->setService('variationLvaAdapter', $mockVariationLvaAdapter);
        $mockLicenceLvaAdapter = m::mock();
        $this->sm->setService('licenceLvaAdapter', $mockLicenceLvaAdapter);
        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockAocEntity = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockAocEntity);
        $mockLocEntity = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLocEntity);
        $mockValidator = m::mock('Zend\Validator\ValidatorInterface');
        $this->sm->setService('CantIncreaseValidator', $mockValidator);
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        // Expectations
        $mockVariationLvaAdapter
            ->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('alterForm')
            ->with($form)
            ->shouldReceive('getIdentifier')
            ->andReturn($id);

        $mockLicenceLvaAdapter
            ->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockApplicationEntity->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($stubbedTolData)
            ->shouldReceive('getTotalAuths')
            ->andReturn($stubbedAuths);

        $mockAocEntity->shouldReceive('getAddressSummaryData')
            ->with($id)
            ->andReturn($stubbedAddressData);

        $mockLocEntity->shouldReceive('getAddressSummaryData')
            ->with($licenceId)
            ->andReturn($stubbedLicenceAddressData);

        $this->controller->shouldReceive('url->fromRoute')
            ->with('create_variation', ['licence' => $id])
            ->andReturn('URL');

        $mockTranslator->shouldReceive('translateReplace')
            ->with('cant-increase-total-vehicles', ['URL'])
            ->andReturn('MESSAGE 1')
            ->shouldReceive('translateReplace')
            ->with('cant-increase-total-trailers', ['URL'])
            ->andReturn('MESSAGE 2');

        $mockValidator->shouldReceive('setGenericMessage')
            ->with('MESSAGE 1')
            ->shouldReceive('setPreviousValue')
            ->with(10)
            ->shouldReceive('setGenericMessage')
            ->with('MESSAGE 2')
            ->shouldReceive('setPreviousValue')
            ->with(5);

        $mockLicenceEntity->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn($stubbedLicData);

        $mockTranslator->shouldReceive('translateReplace')
            ->with('current-authorisation-hint', [10])
            ->andReturn('HINT 10')
            ->shouldReceive('translateReplace')
            ->with('current-authorisation-hint', [5])
            ->andReturn('HINT 5');

        $alteredForm = $this->sut->alterForm($form);

        $this->assertTrue($alteredForm->get('data')->has('totCommunityLicences'));
    }

    public function testAlterFormData()
    {
        // Stubbed data
        $id = 4;
        $data = [
            'foo' => 'bar'
        ];
        $expectedData = [
            'foo' => 'bar',
            'data' => [
                'totCommunityLicences' => 10
            ]
        ];

        // Mock services
        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('getLicenceTotCommunityLicences')
            ->with($id)
            ->andReturn(10);

        $this->assertEquals($expectedData, $this->sut->alterFormData($id, $data));
    }

    public function testFormatCrudDataForForm()
    {
        $this->sut = m::mock('\Common\Controller\Lva\Adapters\VariationOperatingCentreAdapter')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $oc = [
            'id' => 72,
            'address' => [ 'foo', 'countryCode' => ['id' => 'GB'] ]
        ];
        $data = [

            'id' => 4,
            'foo' => 'bar',

            'adPlacedIn' => null,
            'adPlacedDate' => null,
            'adPlaced' => 'N',
            'operatingCentre' => $oc,
        ];

        // should nullify the adPlaced checkbox and split into fieldsets
        $expectedData = [
            'data' => [
                'id' => 4,
                'foo' => 'bar',
            ],
            'advertisements' => [
                'adPlaced' => null,
            ],
            'operatingCentre' => $oc,
            'address' => [ 'foo', 'countryCode' => 'GB' ], // country code is flattened
        ];

        $this->sut->shouldReceive('getOperatingCentreAction')->andReturn('E');
        $this->sut->shouldReceive('getTrafficArea')->andReturn('T');

        $this->assertEquals($expectedData, $this->sut->formatCrudDataForForm($data, 'edit'));
    }

    public function testProcessAddressLookupForm()
    {
        // Don't like mocking the SUT, but mocking the extremely deep abstract methods is less evil
        // than writing extremely tightly coupled tests with tonnes of mocked dependencies
        $this->sut = m::mock('Common\Controller\Lva\Adapters\VariationOperatingCentreAdapter')
            ->makePartial()->shouldAllowMockingProtectedMethods();
        $this->sut->setController($this->controller);
        $this->sut->setServiceLocator($this->sm);

        // Stubbed data
        $childId = 'L1';
        $stubbedTableData = array(
            'L1' => array(
                'id' => 'L1',
                'action' => 'E'
            )
        );

        // Mocked dependencies
        $mockForm = m::mock();
        $mockRequest = m::mock();

        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn($childId);

        $this->sut->shouldReceive('getTableData')
            ->andReturn($stubbedTableData);

        $this->assertFalse($this->sut->processAddressLookupForm($mockForm, $mockRequest));
    }

    public function testProcessAddressLookupFormWithAdd()
    {
        // Don't like mocking the SUT, but mocking the extremely deep abstract methods is less evil
        // than writing extremely tightly coupled tests with tonnes of mocked dependencies
        $this->sut = m::mock('Common\Controller\Lva\Adapters\VariationOperatingCentreAdapter')
            ->makePartial()->shouldAllowMockingProtectedMethods();
        $this->sut->setController($this->controller);
        $this->sut->setServiceLocator($this->sm);

        // Stubbed data
        $childId = null;

        // Mocked dependencies
        $mockForm = m::mock();
        $mockRequest = m::mock();

        // Mock services
        $mockFormHelper = m::mock();
        $this->sm->setService('Helper\Form', $mockFormHelper);

        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn($childId);

        $mockFormHelper->shouldReceive('processAddressLookupForm')
            ->with($mockForm, $mockRequest)
            ->andReturn(true);

        $this->assertTrue($this->sut->processAddressLookupForm($mockForm, $mockRequest));
    }
}
