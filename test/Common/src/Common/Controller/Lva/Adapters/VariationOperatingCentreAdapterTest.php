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
            ->with('lva-variation-operating-centre');

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

        $this->sut->saveActionFormData($mode, $data, $formData);
    }
}
