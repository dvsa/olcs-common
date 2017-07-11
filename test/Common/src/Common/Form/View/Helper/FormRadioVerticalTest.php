<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormRadioVertical;
use Mockery as m;
use Zend\Form\ElementInterface;
use Zend\View\Renderer\RendererInterface;

class FormRadioVerticalTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var FormRadioVertical
     */
    protected $sut;

    public function setUp()
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
