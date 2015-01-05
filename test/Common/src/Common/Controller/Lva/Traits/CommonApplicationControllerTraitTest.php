<?php

/**
 * Common Application Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Bootstrap;
use Common\Service\Entity\ApplicationEntityService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Common Application Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonApplicationControllerTraitTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('CommonTest\Controller\Lva\Traits\Stubs\CommonApplicationControllerTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @group lva_controller_traits
     */
    public function testPreDispatch()
    {
        $id = 4;
        $applicationType = ApplicationEntityService::APPLICATION_TYPE_NEW;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getApplicationType')
            ->andReturn($applicationType);

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('checkForRedirect')
            ->with($id)
            ->andReturn('REDIRECT');

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals('REDIRECT', $this->sut->callPreDispatch());
    }

    /**
     * @group lva_controller_traits
     */
    public function testPreDispatchWithVariation()
    {
        $id = 4;
        $applicationType = ApplicationEntityService::APPLICATION_TYPE_VARIATION;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getApplicationType')
            ->andReturn($applicationType);

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('notFoundAction')
            ->andReturn('ACTION');

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals('ACTION', $this->sut->callPreDispatch());
    }

    /**
     * @group lva_controller_traits
     */
    public function testGetCompletionStatuses()
    {
        $id = 4;

        $mockApplicationCompletion = m::mock();
        $mockApplicationCompletion->shouldReceive('getCompletionStatuses')
            ->with($id)
            ->andReturn('STATUSES');

        $this->sm->setService('Entity\ApplicationCompletion', $mockApplicationCompletion);

        $this->assertEquals('STATUSES', $this->sut->callGetCompletionStatuses($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testUpdateCompletionStatusesWithNullId()
    {
        $id = null;
        $section = 'type_of_licence';

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(3);

        $mockApplicationCompletion = m::mock();
        $mockApplicationCompletion->shouldReceive('updateCompletionStatuses')
            ->with(3, $section);

        $this->sm->setService('Entity\ApplicationCompletion', $mockApplicationCompletion);

        $this->sut->callUpdateCompletionStatuses($id, $section);
    }

    /**
     * @group lva_controller_traits
     */
    public function testUpdateCompletionStatuses()
    {
        $id = 3;
        $section = 'type_of_licence';

        $mockApplicationCompletion = m::mock();
        $mockApplicationCompletion->shouldReceive('updateCompletionStatuses')
            ->with(3, $section);

        $this->sm->setService('Entity\ApplicationCompletion', $mockApplicationCompletion);

        $this->sut->callUpdateCompletionStatuses($id, $section);
    }

    /**
     * @group lva_controller_traits
     */
    public function testIsApplicationNew()
    {
        $id = 4;

        $applicationType = ApplicationEntityService::APPLICATION_TYPE_NEW;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getApplicationType')
            ->andReturn($applicationType);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertTrue($this->sut->callIsApplicationNew($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testIsApplicationNewFalse()
    {
        $id = 4;

        $applicationType = ApplicationEntityService::APPLICATION_TYPE_VARIATION;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getApplicationType')
            ->andReturn($applicationType);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertFalse($this->sut->callIsApplicationNew($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testIsApplicationVariation()
    {
        $id = 4;

        $applicationType = ApplicationEntityService::APPLICATION_TYPE_VARIATION;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getApplicationType')
            ->andReturn($applicationType);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertTrue($this->sut->callIsApplicationVariation($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testIsApplicationVariationFalse()
    {
        $id = 4;

        $applicationType = ApplicationEntityService::APPLICATION_TYPE_NEW;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getApplicationType')
            ->andReturn($applicationType);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertFalse($this->sut->callIsApplicationVariation($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testGetApplicationType()
    {
        $id = 4;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getApplicationType')
            ->andReturn('FOO');

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals('FOO', $this->sut->callGetApplicationType($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testGetApplicationId()
    {
        $id = 4;

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn($id);

        $this->assertEquals($id, $this->sut->callGetApplicationId());
    }

    /**
     * @group lva_controller_traits
     */
    public function testGetLicenceIdWithNull()
    {
        $id = null;

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn(5);

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with(5)
            ->andReturn(7);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals(7, $this->sut->callGetLicenceId($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testGetLicenceId()
    {
        $id = 5;

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with(5)
            ->andReturn(7);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals(7, $this->sut->callGetLicenceId($id));
    }

    /**
     * @group lva_controller_traits
     */
    public function testCompleteSectionWithoutSaveAndContinue()
    {
        $section = 'type_of_licence';

        $this->sut->shouldReceive('addSectionUpdatedMessage')
            ->shouldReceive('isButtonPressed')
            ->with('saveAndContinue')
            ->andReturn(false)
            ->shouldReceive('goToOverviewAfterSave')
            ->andReturn('OVERVIEW');

        $this->assertEquals('OVERVIEW', $this->sut->callCompleteSection($section));
    }

    /**
     * @group lva_controller_traits
     */
    public function testPostSave()
    {
        $id = 3;
        $section = 'type_of_licence';

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn($id);

        $mockApplicationCompletion = m::mock();
        $mockApplicationCompletion->shouldReceive('updateCompletionStatuses')
            ->with($id, $section);

        $this->sm->setService('Entity\ApplicationCompletion', $mockApplicationCompletion);

        $this->sut->callPostSave($section);
    }

    /**
     * @group lva_controller_traits
     */
    public function testCompleteSection()
    {
        $id = 4;
        $section = 'type_of_licence';

        $stubbedOverviewData = array(
            'applicationCompletions' => array(
                array(
                    'foo' => 'bar'
                )
            )
        );
        $stubbedAccessibleSections = array(
            'bar' => 'cake'
        );
        $stubbedSectionStatus = array(
            'type_of_licence' => array(
                'enabled' => true
            ),
            'foo' => array(
                'enabled' => true
            )
        );

        $this->sut->shouldReceive('addSectionUpdatedMessage')
            ->shouldReceive('isButtonPressed')
            ->with('saveAndContinue')
            ->andReturn(true)
            ->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('getAccessibleSections')
            ->with(false)
            ->andReturn($stubbedAccessibleSections)
            ->shouldReceive('setEnabledAndCompleteFlagOnSections')
            ->with(
                $stubbedAccessibleSections, ['foo' => 'bar']
            )
            ->andReturn($stubbedSectionStatus)
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('application')
            ->shouldReceive('redirect->toRouteAjax')
            ->with('lva-application/foo', ['application' => $id])
            ->andReturn('SECTION');

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getOverview')
            ->with($id)
            ->andReturn($stubbedOverviewData);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals('SECTION', $this->sut->callCompleteSection($section));
    }

    /**
     * @group lva_controller_traits
     */
    public function testGoToNextSection()
    {
        $id = 4;
        $section = 'type_of_licence';

        $stubbedOverviewData = array(
            'applicationCompletions' => array(
                array(
                    'foo' => 'bar'
                )
            )
        );
        $stubbedAccessibleSections = array(
            'bar' => 'cake'
        );
        $stubbedSectionStatus = array(
            'type_of_licence' => array(
                'enabled' => true
            ),
            'foo' => array(
                'enabled' => true
            )
        );

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('getAccessibleSections')
            ->with(false)
            ->andReturn($stubbedAccessibleSections)
            ->shouldReceive('setEnabledAndCompleteFlagOnSections')
            ->with(
                $stubbedAccessibleSections, ['foo' => 'bar']
            )
            ->andReturn($stubbedSectionStatus)
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('application')
            ->shouldReceive('redirect->toRouteAjax')
            ->with('lva-application/foo', ['application' => $id])
            ->andReturn('SECTION');

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getOverview')
            ->with($id)
            ->andReturn($stubbedOverviewData);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals('SECTION', $this->sut->callGoToNextSection($section));
    }

    /**
     * @group lva_controller_traits
     */
    public function testGoToNextSectionWithDisabledSection()
    {
        $id = 4;
        $section = 'type_of_licence';

        $stubbedOverviewData = array(
            'applicationCompletions' => array(
                array(
                    'foo' => 'bar'
                )
            )
        );
        $stubbedAccessibleSections = array(
            'bar' => 'cake'
        );
        $stubbedSectionStatus = array(
            'type_of_licence' => array(
                'enabled' => true
            ),
            'foo' => array(
                'enabled' => false
            )
        );

        $this->sut->shouldReceive('getIdentifier')
            ->andReturn($id)
            ->shouldReceive('getAccessibleSections')
            ->with(false)
            ->andReturn($stubbedAccessibleSections)
            ->shouldReceive('setEnabledAndCompleteFlagOnSections')
            ->with(
                $stubbedAccessibleSections, ['foo' => 'bar']
            )
            ->andReturn($stubbedSectionStatus)
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('application')
            ->shouldReceive('goToOverview')
            ->with($id)
            ->andReturn('OVERVIEW');

        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getOverview')
            ->with($id)
            ->andReturn($stubbedOverviewData);

        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals('OVERVIEW', $this->sut->callGoToNextSection($section));
    }
}
