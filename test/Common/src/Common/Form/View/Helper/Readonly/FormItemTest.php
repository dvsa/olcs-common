<?php


namespace CommonTest\Form\View\Helper\Readonly;

use Common\Form\View\Helper\Readonly\FormItem;
use PHPUnit_Framework_TestCase as TestCase;
use Mockery as m;

/**
 * Class FormItemTest
 * @package CommonTest\Form\View\Helper\Readonly
 */
class FormItemTest extends TestCase
{
    public function testInvoke()
    {
        $sut = new FormItem();
        $element = m::mock('Zend\Form\ElementInterface');
        $element->shouldReceive('getValue')->andReturn('test')->once();
        $element->shouldReceive('getOption')->with('disable_html_escape')->andReturnNull()->once();

        $markup = $sut($element);

        $this->assertEquals('test', $markup);
    }

    public function testRender()
    {
        $element = new \Zend\Form\Element();
        $element->setValue('foo<br />');

        $sut = new FormItem();
        $this->assertSame('foo&lt;br /&gt;', $sut->render($element));
    }

    public function testRenderNoEscape()
    {
        $element = new \Zend\Form\Element();
        $element->setValue('foo<br />');
        $element->setOption('disable_html_escape', true);

        $sut = new FormItem();
        $this->assertSame('foo<br />', $sut->render($element));
    }
}
