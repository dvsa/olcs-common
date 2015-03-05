<?php

/**
 * Application Snapshot Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Processing\ApplicationSnapshotProcessingService;
use CommonTest\Bootstrap;
use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Data\CategoryDataService;

/**
 * Application Snapshot Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSnapshotProcessingServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationSnapshotProcessingService();

        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerNewApplication
     */
    public function testStoreSnapshotNewApplicationOnGrant($stubbedTol, $code)
    {
        // Params
        $applicationId = 123;
        $event = ApplicationSnapshotProcessingService::ON_GRANT;

        // Expected data
        $expectedDocumentData = [
            'identifier' => 'ABCDEF',
            'application' => 123,
            'licence' => 321,
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL,
            'filename' => $code . ' Application Snapshot Grant.html',
            'fileExtension' => 'doc_html',
            'issuedDate' => '2015-01-01 10:10:10',
            'description' => $code . ' Application Snapshot (at grant/valid)',
            'isDigital' => false,
            'isScan' => false
        ];

        // Mocks
        $mockApplicationEntity = m::mock();
        $mockControllerPluginManager = m::mock();
        $mockApplication = m::mock();
        $mockMvcEvent = m::mock();
        $mockAdapter = m::mock();
        $mockControllerManager = m::mock();
        $mockController = m::mock();
        $mockView = m::mock();
        $mockViewRenderer = m::mock();
        $mockFileUploader = m::mock();
        $mockFile = m::mock();
        $mockDocumentEntity = m::mock();
        $mockDate = m::mock();

        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $this->sm->setService('ControllerPluginManager', $mockControllerPluginManager);
        $this->sm->setService('Application', $mockApplication);
        $this->sm->setService('ApplicationReviewAdapter', $mockAdapter);
        $this->sm->setService('ControllerManager', $mockControllerManager);
        $this->sm->setService('ViewRenderer', $mockViewRenderer);
        $this->sm->setService('FileUploader', $mockFileUploader);
        $this->sm->setService('Entity\Document', $mockDocumentEntity);
        $this->sm->setService('Helper\Date', $mockDate);

        // Expectations
        $mockApplicationEntity->shouldReceive('getApplicationType')
            ->with(123)
            ->andReturn(ApplicationEntityService::APPLICATION_TYPE_NEW)
            ->shouldReceive('getLicenceIdForApplication')
            ->with(123)
            ->andReturn(321)
            ->shouldReceive('getTypeOfLicenceData')
            ->with(123)
            ->andReturn($stubbedTol);

        $mockApplication->shouldReceive('getMvcEvent')
            ->andReturn($mockMvcEvent);

        $mockControllerManager->shouldReceive('get')
            ->with('LvaApplication/Review')
            ->andReturn($mockController);

        $mockController->shouldReceive('setPluginManager')
            ->with($mockControllerPluginManager)
            ->shouldReceive('setEvent')
            ->with($mockMvcEvent)
            ->shouldReceive('setAdapter')
            ->with($mockAdapter)
            ->shouldReceive('indexAction')
            ->andReturn($mockView);

        $mockViewRenderer->shouldReceive('render')
            ->with($mockView)
            ->andReturn('HTML');

        $mockFileUploader->shouldReceive('getUploader')
            ->andReturnSelf()
            ->shouldReceive('setFile')
            ->with(['content' => 'HTML'])
            ->shouldReceive('upload')
            ->andReturn($mockFile);

        $mockDate->shouldReceive('getDate')
            ->with('Y-m-d H:i:s')
            ->andReturn('2015-01-01 10:10:10');

        $mockFile->shouldReceive('getIdentifier')
            ->andReturn('ABCDEF');

        $mockDocumentEntity->shouldReceive('save')
            ->with($expectedDocumentData);

        $this->sut->storeSnapshot($applicationId, $event);
    }

    /**
     * @dataProvider providerNewApplication
     */
    public function testStoreSnapshotNewApplicationOnSubmit($stubbedTol, $code)
    {
        // Params
        $applicationId = 123;
        $event = ApplicationSnapshotProcessingService::ON_SUBMIT;

        // Expected data
        $expectedDocumentData = [
            'identifier' => 'ABCDEF',
            'application' => 123,
            'licence' => 321,
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL,
            'filename' => $code . ' Application Snapshot Submit.html',
            'fileExtension' => 'doc_html',
            'issuedDate' => '2015-01-01 10:10:10',
            'description' => $code . ' Application Snapshot (at submission)',
            'isDigital' => true,
            'isScan' => false
        ];

        // Mocks
        $mockApplicationEntity = m::mock();
        $mockControllerPluginManager = m::mock();
        $mockApplication = m::mock();
        $mockMvcEvent = m::mock();
        $mockAdapter = m::mock();
        $mockControllerManager = m::mock();
        $mockController = m::mock();
        $mockView = m::mock();
        $mockViewRenderer = m::mock();
        $mockFileUploader = m::mock();
        $mockFile = m::mock();
        $mockDocumentEntity = m::mock();
        $mockDate = m::mock();

        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $this->sm->setService('ControllerPluginManager', $mockControllerPluginManager);
        $this->sm->setService('Application', $mockApplication);
        $this->sm->setService('ApplicationReviewAdapter', $mockAdapter);
        $this->sm->setService('ControllerManager', $mockControllerManager);
        $this->sm->setService('ViewRenderer', $mockViewRenderer);
        $this->sm->setService('FileUploader', $mockFileUploader);
        $this->sm->setService('Entity\Document', $mockDocumentEntity);
        $this->sm->setService('Helper\Date', $mockDate);

        // Expectations
        $mockApplicationEntity->shouldReceive('getApplicationType')
            ->with(123)
            ->andReturn(ApplicationEntityService::APPLICATION_TYPE_NEW)
            ->shouldReceive('getLicenceIdForApplication')
            ->with(123)
            ->andReturn(321)
            ->shouldReceive('getTypeOfLicenceData')
            ->with(123)
            ->andReturn($stubbedTol);

        $mockApplication->shouldReceive('getMvcEvent')
            ->andReturn($mockMvcEvent);

        $mockControllerManager->shouldReceive('get')
            ->with('LvaApplication/Review')
            ->andReturn($mockController);

        $mockController->shouldReceive('setPluginManager')
            ->with($mockControllerPluginManager)
            ->shouldReceive('setEvent')
            ->with($mockMvcEvent)
            ->shouldReceive('setAdapter')
            ->with($mockAdapter)
            ->shouldReceive('indexAction')
            ->andReturn($mockView);

        $mockViewRenderer->shouldReceive('render')
            ->with($mockView)
            ->andReturn('HTML');

        $mockFileUploader->shouldReceive('getUploader')
            ->andReturnSelf()
            ->shouldReceive('setFile')
            ->with(['content' => 'HTML'])
            ->shouldReceive('upload')
            ->andReturn($mockFile);

        $mockDate->shouldReceive('getDate')
            ->with('Y-m-d H:i:s')
            ->andReturn('2015-01-01 10:10:10');

        $mockFile->shouldReceive('getIdentifier')
            ->andReturn('ABCDEF');

        $mockDocumentEntity->shouldReceive('save')
            ->with($expectedDocumentData);

        $this->sut->storeSnapshot($applicationId, $event);
    }

    /**
     * @dataProvider providerVariation
     */
    public function testStoreSnapshotVariationOnGrant($stubbedTol, $code, $isUpgrade)
    {
        // Params
        $applicationId = 123;
        $event = ApplicationSnapshotProcessingService::ON_GRANT;

        // Expected data
        $expectedDocumentData = [
            'identifier' => 'ABCDEF',
            'application' => 123,
            'licence' => 321,
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_FORMS_ASSISTED_DIGITAL,
            'filename' => $code . ' Application Snapshot Grant.html',
            'fileExtension' => 'doc_html',
            'issuedDate' => '2015-01-01 10:10:10',
            'description' => $code . ' Application Snapshot (at grant/valid)',
            'isDigital' => false,
            'isScan' => false
        ];

        // Mocks
        $mockApplicationEntity = m::mock();
        $mockControllerPluginManager = m::mock();
        $mockApplication = m::mock();
        $mockMvcEvent = m::mock();
        $mockAdapter = m::mock();
        $mockControllerManager = m::mock();
        $mockController = m::mock();
        $mockView = m::mock();
        $mockViewRenderer = m::mock();
        $mockFileUploader = m::mock();
        $mockFile = m::mock();
        $mockDocumentEntity = m::mock();
        $mockDate = m::mock();
        $mockVariationSection = m::mock();

        $this->sm->setService('Entity\Application', $mockApplicationEntity);
        $this->sm->setService('ControllerPluginManager', $mockControllerPluginManager);
        $this->sm->setService('Application', $mockApplication);
        $this->sm->setService('VariationReviewAdapter', $mockAdapter);
        $this->sm->setService('ControllerManager', $mockControllerManager);
        $this->sm->setService('ViewRenderer', $mockViewRenderer);
        $this->sm->setService('FileUploader', $mockFileUploader);
        $this->sm->setService('Entity\Document', $mockDocumentEntity);
        $this->sm->setService('Helper\Date', $mockDate);
        $this->sm->setService('Processing\VariationSection', $mockVariationSection);

        // Expectations
        $mockApplicationEntity->shouldReceive('getApplicationType')
            ->with(123)
            ->andReturn(ApplicationEntityService::APPLICATION_TYPE_VARIATION)
            ->shouldReceive('getLicenceIdForApplication')
            ->with(123)
            ->andReturn(321)
            ->shouldReceive('getTypeOfLicenceData')
            ->with(123)
            ->andReturn($stubbedTol);

        $mockApplication->shouldReceive('getMvcEvent')
            ->andReturn($mockMvcEvent);

        $mockControllerManager->shouldReceive('get')
            ->with('LvaVariation/Review')
            ->andReturn($mockController);

        $mockController->shouldReceive('setPluginManager')
            ->with($mockControllerPluginManager)
            ->shouldReceive('setEvent')
            ->with($mockMvcEvent)
            ->shouldReceive('setAdapter')
            ->with($mockAdapter)
            ->shouldReceive('indexAction')
            ->andReturn($mockView);

        $mockViewRenderer->shouldReceive('render')
            ->with($mockView)
            ->andReturn('HTML');

        $mockFileUploader->shouldReceive('getUploader')
            ->andReturnSelf()
            ->shouldReceive('setFile')
            ->with(['content' => 'HTML'])
            ->shouldReceive('upload')
            ->andReturn($mockFile);

        $mockDate->shouldReceive('getDate')
            ->with('Y-m-d H:i:s')
            ->andReturn('2015-01-01 10:10:10');

        $mockFile->shouldReceive('getIdentifier')
            ->andReturn('ABCDEF');

        $mockDocumentEntity->shouldReceive('save')
            ->with($expectedDocumentData);

        $mockVariationSection->shouldReceive('isRealUpgrade')
            ->with(123)
            ->andReturn($isUpgrade);

        $this->sut->storeSnapshot($applicationId, $event);
    }

    public function providerNewApplication()
    {
        return [
            [
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                ],
                'GV79'
            ],
            [
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
                    'licenceType' => LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED
                ],
                'PSV356'
            ],
            [
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV,
                    'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                ],
                'PSV421'
            ]
        ];
    }

    public function providerVariation()
    {
        return [
            [
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                ],
                'GV80A',
                true
            ],
            [
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                ],
                'GV81',
                false
            ],
            [
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV
                ],
                'PSV431A',
                true
            ],
            [
                [
                    'goodsOrPsv' => LicenceEntityService::LICENCE_CATEGORY_PSV
                ],
                'PSV431',
                false
            ]
        ];
    }
}
