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
        $element->shouldReceive('getValue')->andReturn('test');

        $markup = $sut($element);

        $this->assertEquals('test', $markup);
    }
}
