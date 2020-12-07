<?php

namespace CommonTest\Form\View\Helper\Readonly;

use Common\Form\Elements;
use Common\Form\View\Helper\Readonly\FormItem;

/**
 * @covers \Common\Form\View\Helper\Readonly\FormItem
 */
class FormItemTest extends \PHPUnit\Framework\TestCase
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
                'element' => (new \Laminas\Form\Element())
                    ->setValue('foo<br />'),
                'expect' => 'foo&lt;br /&gt;',
            ],
            'common;htmlEscapeOff' => [
                'element' => (new \Laminas\Form\Element())
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
                'element' => (new \Laminas\Form\Element\Button()),
                'expect' => '',
            ],
            'input:submit' => [
                'element' => (new \Laminas\Form\Element\Submit()),
                'expect' => '',
            ],
            'input:hidden' => [
                'element' => (new \Laminas\Form\Element\Hidden()),
                'expect' => '',
            ],
        ];
    }
}
