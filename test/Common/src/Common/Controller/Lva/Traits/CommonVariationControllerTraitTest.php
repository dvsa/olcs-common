<?php

/**
 * Common Variation Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Common Variation Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommonVariationControllerTraitTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('CommonTest\Controller\Lva\Traits\Stubs\CommonVariationControllerTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testPreDispatchWith404()
    {
        $appId = 3;

        $this->sut->shouldReceive('getApplicationId')
            ->andReturn($appId)
            ->shouldReceive('isApplicationVariation')
            ->with($appId)
            ->andReturn(false)
            ->shouldReceive('notFoundAction')
            ->andReturn(404);

        $this->assertEquals(404, $this->sut->callPreDispatch());
    }

    public function testPreDispatch()
    {
        $appId = 3;

        $this->sut->shouldReceive('getApplicationId')
            ->andReturn($appId)
            ->shouldReceive('isApplicationVariation')
            ->with($appId)
            ->andReturn(true)
            ->shouldReceive('checkForRedirect')
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->callPreDispatch());
    }

    public function testPostSave()
    {
        // Params
        $appId = 3;
        $section = 'foo';

        // Mocks
        $mockProcessor = m::mock();
        $this->sm->setService('Processing\VariationSection', $mockProcessor);

        // Expectations
        $this->sut->shouldReceive('getApplicationId')
            ->andReturn($appId);

        $mockProcessor->shouldReceive('setApplicationId')
            ->with($appId)
            ->andReturnSelf()
            ->shouldReceive('completeSection')
            ->with($section);

        $this->sut->callPostSave($section);
    }

    public function testGoToNextSectionOverview()
    {
        // Params and data
        $section = 'foo';
        $accessibleSections = [
            'bar',
            'foo'
        ];
        $appId = 4;

        // Expectations
        $this->sut->shouldReceive('getAccessibleSections')
            ->andReturn($accessibleSections)
            ->shouldReceive('getApplicationId')
            ->andReturn($appId)
            ->shouldReceive('goToOverview')
            ->with($appId)
            ->andReturn('OVERVIEW');

        $this->assertEquals('OVERVIEW', $this->sut->callGoToNextSection($section));
    }

    public function testGoToNextSection()
    {
        // Params and data
        $section = 'foo';
        $accessibleSections = [
            'bar',
            'foo',
            'cake'
        ];
        $appId = 4;

        // Expectations
        $this->sut->shouldReceive('getAccessibleSections')
            ->andReturn($accessibleSections)
            ->shouldReceive('getApplicationId')
            ->andReturn($appId)
            ->shouldReceive('getIdentifierIndex')
            ->andReturn('application');

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->with('lva-variation/cake', ['application' => 4])
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->callGoToNextSection($section));
    }
}
