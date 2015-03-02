<?php

/**
 * Licence Operating Centres Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Licence Operating Centres Controller Trait Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceOperatingCentresControllerTraitTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('\CommonTest\Controller\Lva\Traits\Stubs\LicenceOperatingCentresControllerTraitStub')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider addActionConditionalProvider
     */
    public function testAddAction($conditional)
    {
        // Mocks
        $mockProcessingService = m::mock();
        $this->sm->setService('Processing\CreateVariation', $mockProcessingService);
        $mockRequest = m::mock();
        $mockForm = m::mock('\Zend\Form\Form');

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('render')
            ->with(
                'oc-create-variation-confirmation-title',
                $mockForm,
                ['sectionText' => 'oc-create-variation-confirmation-message']
            )
            ->andReturn('RENDER');

        $mockProcessingService->shouldReceive('getForm')
            ->with($mockRequest)
            ->andReturn($mockForm);

        // @NOTE The data provider, provides multiple routes into the same if statement
        // I think this solution is quite elegant, rather than duplicating the test with all of the same expectations
        // I don't think this solution should be used on complicated units of code with multiple nested conditionals etc
        // but for units of code where there is just a single conditional with multiple routes in, I think this fits the
        // bill nicely
        $conditional($mockRequest, $mockForm);

        $this->assertEquals('RENDER', $this->sut->callAddAction());
    }

    public function testAddActionWithPost()
    {
        $formData = [
            'foo' => 'bar'
        ];

        // Mocks
        $mockProcessingService = m::mock();
        $this->sm->setService('Processing\CreateVariation', $mockProcessingService);
        $mockRequest = m::mock();
        $mockForm = m::mock('\Zend\Form\Form');

        // Expectations
        $this->sut->shouldReceive('getRequest')
            ->andReturn($mockRequest)
            ->shouldReceive('params')
            ->with('licence')
            ->andReturn(123);

        $mockForm->shouldReceive('isValid')
            ->andReturn(true);

        $mockProcessingService->shouldReceive('getForm')
            ->with($mockRequest)
            ->andReturn($mockForm)
            ->shouldReceive('getDataFromForm')
            ->with($mockForm)
            ->andReturn($formData)
            ->shouldReceive('createVariation')
            ->with(123, $formData)
            ->andReturn(321);

        $mockRequest->shouldReceive('isPost')
            ->andReturn(true);

        $this->sut->shouldReceive('redirect->toRouteAjax')
            ->with('lva-variation', ['application' => 321])
            ->andReturn('REDIRECT');

        $this->assertEquals('REDIRECT', $this->sut->callAddAction());
    }

    public function addActionConditionalProvider()
    {
        return [
            'Without post' => [
                function ($mockRequest) {
                    $mockRequest->shouldReceive('isPost')
                        ->andReturn(false);
                }
            ],
            'Without valid form' => [
                function ($mockRequest, $mockForm) {
                    $mockRequest->shouldReceive('isPost')
                        ->andReturn(true);

                    $mockForm->shouldReceive('isValid')
                        ->andReturn(false);
                }
            ]
        ];
    }
}
