<?php

namespace CommonTest\Form\View\Helper;

use Common\Form\View\Helper\FormRadioOption;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Zend\Form\Element\Radio;

class FormRadioOptionTest extends TestCase
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
            '<input type="radio" name="NAME" value="B"><label>bbb</label>',
            $rendered
        );
        $rendered = $this->sut->__invoke($radioElement, 'A');
        $this->assertSame(
            '<input type="radio" name="NAME" value="A"><label>aaa</label>',
            $rendered
        );
    }
}
