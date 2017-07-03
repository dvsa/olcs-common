<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormRadioHorizontal;
use Mockery as m;
use Zend\Form\ElementInterface;
use Zend\View\Renderer\RendererInterface;

class FormRadioHorizontalTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var FormRadioHorizontal
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new FormRadioHorizontal();
    }

    public function testInvoke()
    {
        $mockElement = m::mock(ElementInterface::class);
        $mockView = m::mock(RendererInterface::class);
        $mockView->shouldReceive('partial')->with('partials/form/radio-horizontal', ['element' => $mockElement])
            ->once();

        $this->sut->setView($mockView);

        $this->sut->__invoke($mockElement);
    }
}
