<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormRadioHorizontal;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Zend\Form\ElementInterface;
use Zend\View\Renderer\RendererInterface;

class FormRadioHorizontalTest extends TestCase
{
    /**
     * @var FormRadioHorizontal
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new FormRadioHorizontal();
    }

    public function testInvoke()
    {
        $mockElement = m::mock(ElementInterface::class);
        $mockView = m::mock(RendererInterface::class);
        $mockView->shouldReceive('vars->getArrayCopy')->with()->andReturn(['VAR' => 'FOO']);
        $mockView->shouldReceive('render')
            ->with('partials/form/radio-horizontal', ['VAR' => 'FOO', 'element' => $mockElement])->once();

        $this->sut->setView($mockView);

        $this->sut->__invoke($mockElement);
    }
}
