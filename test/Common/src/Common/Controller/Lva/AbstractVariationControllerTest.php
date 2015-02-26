<?php

/**
 * Abstract Variation Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Abstract Variation Controller Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractVariationControllerTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = m::mock('\Common\Controller\Lva\AbstractVariationController')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider indexActionConditionalProvider
     */
    public function testIndexAction($conditional)
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
                'create-variation-confirmation',
                $mockForm,
                ['sectionText' => 'licence.variation.confirmation.text']
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

        $this->assertEquals('RENDER', $this->sut->indexAction());
    }

    public function testIndexActionWithPost()
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

        $this->assertEquals('REDIRECT', $this->sut->indexAction());
    }

    public function indexActionConditionalProvider()
    {
        return [
            [
                function ($mockRequest) {
                    $mockRequest->shouldReceive('isPost')
                        ->andReturn(false);
                }
            ],
            [
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
