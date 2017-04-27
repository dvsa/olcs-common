<?php

namespace CommonTest\Form\View\Helper\Readonly;

use Common\Form\Elements;
use Common\Form\View\Helper\Readonly\FormItem;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers \Common\Form\View\Helper\Readonly\FormItem
 */
class FormItemTest extends TestCase
{
    public function testInvokeSelf()
    {
        $sut = new FormItem();

        static::assertSame($sut, $sut(null));
    }

    /**
     * @dataProvider dpTestRender
     */
    public function testRender($element, $expect)
    {
        $sut = new FormItem();
        static::assertSame($expect, $sut->render($element));
    }

    public function dpTestRender()
    {
        return [
            'common' => [
                'element' => (new \Zend\Form\Element())
                    ->setValue('foo<br />'),
                'expect' => 'foo&lt;br /&gt;',
            ],
            'common;htmlEscapeOff' => [
                'element' => (new \Zend\Form\Element())
                    ->setValue('foo<br />')
                    ->setOption('disable_html_escape', true),
                'expect' => 'foo<br />',
            ],
            'ActionButton' => [
                'element' => (new Elements\InputFilters\ActionButton()),
                'expect' => '',
            ],
            'AttachFilesButton' => [
                'element' => (new Elements\Types\AttachFilesButton()),
                'expect' => '',
            ],
            'Button' => [
                'element' => (new \Zend\Form\Element\Button()),
                'expect' => '',
            ],
            'input:submit' => [
                'element' => (new \Zend\Form\Element\Submit()),
                'expect' => '',
            ],
            'input:hidden' => [
                'element' => (new \Zend\Form\Element\Hidden()),
                'expect' => '',
            ],
        ];
    }
}
