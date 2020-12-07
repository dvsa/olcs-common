<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormCheckboxAdvanced;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\Form\ElementInterface;
use Laminas\View\Renderer\RendererInterface;
use Laminas\View\Model\ViewModel;

class FormCheckboxAdvancedTest extends MockeryTestCase
{
    /**
     * @var FormCheckboxAdvanced
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new FormCheckboxAdvanced();
    }

    public function testInvoke()
    {
        $mockElement = m::mock(ElementInterface::class)
            ->shouldReceive('getOption')
            ->with('content')
            ->andReturn('content')
            ->once()
            ->getMock();
        $mockView = m::mock(RendererInterface::class)->makePartial();
        $mockView->data = 'data';
        $mockView
            ->shouldReceive('partial')
            ->with(
                'partials/form/checkbox-advanced',
                ['element' => $mockElement, 'content' => 'content', 'data' => 'data']
            )
            ->once()
            ->getMock();

        $this->sut->setView($mockView);
        $this->sut->__invoke($mockElement);
    }
}
