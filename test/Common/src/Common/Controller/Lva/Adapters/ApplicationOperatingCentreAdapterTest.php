<?php

/**
 * Application Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationOperatingCentreAdapter;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Operating Centre Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationOperatingCentreAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut = new ApplicationOperatingCentreAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function testDelete()
    {
        // Stubbed data
        $childId = 1;
        $applicationId = 5;

        // Mocked services
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockEntityService);
        $mockLvaAdapter = m::mock();
        $this->sm->setService('ApplicationLvaAdapter', $mockLvaAdapter);

        // Expectations
        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn($childId);

        $mockEntityService->shouldReceive('delete')
            ->with(1);

        $mockLvaAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($applicationId);

        $mockEntityService->shouldReceive('getOperatingCentresCount')
            ->with($applicationId)
            ->andReturn(['Count' => 1]);

        $this->sut->delete();
    }

    public function testDeleteWithoutRowsLeft()
    {
        // Stubbed data
        $childId = 1;
        $applicationId = 5;
        $licenceId = 4;

        // Mocked services
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockEntityService);
        $mockLvaAdapter = m::mock();
        $this->sm->setService('ApplicationLvaAdapter', $mockLvaAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockLicenceLvaAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceLvaAdapter);

        // Expectations
        $this->controller->shouldReceive('params')
            ->with('child_id')
            ->andReturn($childId);

        $mockEntityService->shouldReceive('delete')
            ->with(1);

        $mockLvaAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($applicationId);

        $mockEntityService->shouldReceive('getOperatingCentresCount')
            ->with($applicationId)
            ->andReturn(['Count' => 0]);

        $mockLicenceLvaAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('setTrafficArea')
            ->with($licenceId, null)
            ->andReturnSelf();

        $mockLicenceEntity->shouldReceive('setEnforcementArea')
            ->with($licenceId, null)
            ->andReturnSelf();

        $this->sut->delete();
    }

    public function testCheckTrafficAreaAfterCrudActionWithEdit()
    {
        // Stubbed data
        $data = [
            'action' => []
        ];

        $this->assertNull($this->sut->checkTrafficAreaAfterCrudAction($data));
    }

    public function testCheckTrafficAreaAfterCrudActionWithTrafficArea()
    {
        // Stubbed data
        $data = [
            'action' => 'add'
        ];
        $licenceId = 3;

        // Mocked services
        $mockLicenceLvaAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceLvaAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        // Expectations
        $mockLicenceLvaAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn('foo');

        $this->assertNull($this->sut->checkTrafficAreaAfterCrudAction($data));
    }

    public function testCheckTrafficAreaAfterCrudActionWithTrafficAreaInPost()
    {
        // Stubbed data
        $data = [
            'action' => 'add'
        ];
        $licenceId = 3;
        $post = [
            'dataTrafficArea' => [
                'trafficArea' => 'A'
            ]
        ];

        // Mocked services
        $mockLicenceLvaAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceLvaAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        // Expectations
        $mockLicenceLvaAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn(null);

        $this->controller->shouldReceive('getRequest->getPost')
            ->andReturn($post);

        $this->assertNull($this->sut->checkTrafficAreaAfterCrudAction($data));
    }

    public function testCheckTrafficAreaAfterCrudActionWithoutTrafficAreaWithoutOperatingCentre()
    {
        // Stubbed data
        $data = [
            'action' => 'add'
        ];
        $licenceId = 3;
        $post = [];
        $applicationId = 6;

        // Mocked services
        $mockLicenceLvaAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceLvaAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockLvaAdapter = m::mock();
        $this->sm->setService('ApplicationLvaAdapter', $mockLvaAdapter);
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockEntityService);

        // Expectations
        $mockLicenceLvaAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn(null);

        $this->controller->shouldReceive('getRequest->getPost')
            ->andReturn($post);

        $mockLvaAdapter->shouldreceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($applicationId);

        $mockEntityService->shouldReceive('getOperatingCentresCount')
            ->with($applicationId)
            ->andReturn(['Count' => 0]);

        $this->assertNull($this->sut->checkTrafficAreaAfterCrudAction($data));
    }

    public function testCheckTrafficAreaAfterCrudActionWithoutTrafficAreaWithOperatingCentre()
    {
        // Stubbed data
        $data = [
            'action' => 'add'
        ];
        $licenceId = 3;
        $post = [];
        $applicationId = 6;

        // Mocked services
        $mockLicenceLvaAdapter = m::mock();
        $this->sm->setService('LicenceLvaAdapter', $mockLicenceLvaAdapter);
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);
        $mockLvaAdapter = m::mock();
        $this->sm->setService('ApplicationLvaAdapter', $mockLvaAdapter);
        $mockEntityService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockEntityService);
        $mockFlashMessenger = m::mock();
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);

        // Expectations
        $mockLicenceLvaAdapter->shouldReceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($licenceId);

        $mockLicenceEntity->shouldReceive('getTrafficArea')
            ->with($licenceId)
            ->andReturn(null);

        $this->controller->shouldReceive('getRequest->getPost')
            ->andReturn($post);

        $mockLvaAdapter->shouldreceive('setController')
            ->with($this->controller)
            ->shouldReceive('getIdentifier')
            ->andReturn($applicationId);

        $mockEntityService->shouldReceive('getOperatingCentresCount')
            ->with($applicationId)
            ->andReturn(['Count' => 1]);

        $mockFlashMessenger->shouldReceive('addWarningMessage')
            ->with('select-traffic-area-error');

        $this->controller->shouldReceive('redirect->toRoute')
            ->with(null, [], [], true)
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->checkTrafficAreaAfterCrudAction($data));
    }

    public function testFormatDataForFormSetsEnforcementArea()
    {
        $sut = m::mock('Common\Controller\Lva\Adapters\ApplicationOperatingCentreAdapter')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $data = [
            'licence' => [
                'enforcementArea' => ['id' => 'V048'],
            ],
        ];
        $tableData = [];
        $licenceData = [
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
        ];

        $result = $sut->formatDataForForm($data, $tableData, $licenceData);
        $this->assertEquals('V048', $result['dataTrafficArea']['enforcementArea']);
    }

    public function testAlterActionFormForPsvRestricted()
    {
        $sut = m::mock('Common\Controller\Lva\Adapters\ApplicationOperatingCentreAdapter')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mockForm = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();

        $licenceData = [
            'licenceType' => LicenceEntityService::LICENCE_TYPE_RESTRICTED,
        ];

        $mockForm->shouldReceive('get->get');
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'data->noOfTrailersRequired')->once();
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'advertisements')->once();
        $mockFormHelper->shouldReceive('alterElementLabel')->times(3);

        $sut->shouldReceive('getTypeOfLicenceData')->once()->andReturn($licenceData);
        $sut->shouldReceive('getServiceLocator->get')->twice()->with('Helper\Form')->andReturn($mockFormHelper);
        $mockFormHelper->shouldReceive('attachValidator')
            ->once()
            ->with($mockForm, 'data->noOfVehiclesRequired', m::type('\Zend\Validator\LessThan'));

        $sut->alterActionFormForPsv($mockForm);

    }

    public function testAlterActionFormForPsvNotRestricted()
    {
        $sut = m::mock('Common\Controller\Lva\Adapters\ApplicationOperatingCentreAdapter')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $mockForm = m::mock('\Zend\Form\Form');
        $mockFormHelper = m::mock();
        $sut->shouldReceive('getServiceLocator->get')->once()->with('Helper\Form')->andReturn($mockFormHelper);

        $licenceData = [
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
        ];

        $mockForm->shouldReceive('get->get');
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'data->noOfTrailersRequired')->once();
        $mockFormHelper->shouldReceive('remove')->with($mockForm, 'advertisements')->once();
        $mockFormHelper->shouldReceive('alterElementLabel')->times(3);

        $sut->shouldReceive('getTypeOfLicenceData')->once()->andReturn($licenceData);

        $sut->alterActionFormForPsv($mockForm);
    }
}
