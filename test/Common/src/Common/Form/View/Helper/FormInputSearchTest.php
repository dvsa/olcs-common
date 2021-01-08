<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormInputSearch;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\Form\ElementInterface;
use Laminas\View\Renderer\RendererInterface;

class FormInputSearchTest extends TestCase
{
    /**
     * @var FormInputSearch
     */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new FormInputSearch();
    }

    public function testInvoke()
    {
        $mockElement = m::mock(ElementInterface::class);
        $mockView = m::mock(RendererInterface::class);
        $mockView->shouldReceive('vars->getArrayCopy')->withNoArgs()->andReturn(['VAR' => 'FOO']);
        $mockView->shouldReceive('render')
            ->with('partials/form/input-search', ['VAR' => 'FOO', 'fieldsetElement' => $mockElement])->once();

        $this->sut->setView($mockView);

        $this->sut->__invoke($mockElement);
    }
}
