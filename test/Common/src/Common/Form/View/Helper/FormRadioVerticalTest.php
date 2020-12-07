<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormRadioVertical;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\Form\ElementInterface;
use Laminas\View\Renderer\RendererInterface;

class FormRadioVerticalTest extends TestCase
{
    /**
     * @var FormRadioVertical
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new FormRadioVertical();
    }

    public function testInvoke()
    {
        $mockElement = m::mock(ElementInterface::class);
        $mockView = m::mock(RendererInterface::class);
        $mockView->shouldReceive('vars->getArrayCopy')->with()->andReturn(['VAR' => 'FOO']);
        $mockView->shouldReceive('render')
            ->with('partials/form/radio-vertical', ['VAR' => 'FOO', 'element' => $mockElement])->once();

        $this->sut->setView($mockView);

        $this->sut->__invoke($mockElement);
    }
}
