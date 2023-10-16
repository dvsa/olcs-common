<?php

namespace CommonTest\Controller\Traits;

use Common\Controller\Traits\GenericMethods;
use CommonTest\Controller\Traits\Stubs\GenericMethodsStub;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * @covers \Common\Controller\Traits\GenericMethods
 */
class GenericMethodsTest extends MockeryTestCase
{
    /** @var  GenericMethods | m\MockInterface */
    private $sut;

    /** @var  m\MockInterface | \Laminas\ServiceManager\ServiceLocatorInterface */
    private $mockSm;
    /** @var  m\MockInterface | \Common\Service\Helper\FormHelperService */
    private $mockHlpForm;

    public function setUp(): void
    {
        $this->mockHlpForm = m::mock(\Common\Service\Helper\FormHelperService::class);

        $this->sut = m::mock(GenericMethodsStub::class, [$this->mockHlpForm])->makePartial();
        $this->sut->shouldReceive('getServiceLocator')->andReturn($this->mockSm);
    }

    public function testGetForm()
    {
        $class = 'unit_path_to_class';

        $mockReq = m::mock(\Laminas\Http\Request::class);
        $mockForm = m::mock(\Laminas\Form\Form::class);

        $this->sut
            ->shouldReceive('getRequest')->twice()->andReturn($mockReq);

        $this->mockHlpForm
            ->shouldReceive('createForm')->once()->with($class)->andReturn($mockForm)
            ->shouldReceive('setFormActionFromRequest')->once()->with($mockForm, $mockReq)
            ->shouldReceive('processAddressLookupForm')->once()->with($mockForm, $mockReq);

        static::assertSame($mockForm, $this->sut->getForm($class));
    }

    public function testGenerateFormWithData()
    {
        $class = 'unit_path_to_class';
        $callback = function () {
        };
        $data = ['unit_data'];
        $fieldVals = ['unit_fieldValues'];

        $mockReq = m::mock(\Laminas\Http\Request::class);
        $mockReq->shouldReceive('isPost')->once()->andReturn(false);

        $mockForm = m::mock(\Laminas\Form\Form::class);
        $mockForm->shouldReceive('setData')->once()->with($data);

        $this->sut
            ->shouldReceive('getRequest')->once()->andReturn($mockReq)
            ->shouldReceive('getForm')->once()->with($class)->andReturn($mockForm)
            ->shouldReceive('formPost')->once()->with($mockForm, $callback, [], true, $fieldVals)->andReturn($mockForm);

        static::assertSame($mockForm, $this->sut->generateFormWithData($class, $callback, $data, false, $fieldVals));
    }
}
