<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormRadioOption;
use Mockery as m;
use Zend\Form\Element\Radio;

class FormRadioOptionTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var FormRadioOption
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new FormRadioOption();
    }

    public function testInvokeNull()
    {
        $this->assertSame($this->sut, $this->sut->__invoke());
    }

    public function testInvoke()
    {
        $radioElement = new Radio('NAME');
        $radioElement->setValueOptions(['A' => 'aaa', 'B' => 'bbb']);

        $rendered = $this->sut->__invoke($radioElement, 'B');
        $this->assertSame(
            '<input type="radio" name="NAME" value="B"><div ><label>bbb</label></div>',
            $rendered
        );
        $rendered = $this->sut->__invoke($radioElement, 'A');
        $this->assertSame(
            '<input type="radio" name="NAME" value="A"><div ><label>aaa</label></div>',
            $rendered
        );
    }
}
