<?php

/**
 * Content Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table;

use Common\Service\Table\ContentHelper;

/**
 * Content Helper Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

class ContentHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Setup the content helper
     */
    public function getContentHelper($mock)
    {
        return new ContentHelper(__DIR__ . '/TestResources', $mock);
    }

    /**
     * Test renderLayout with missing partial
     *
     * @expectedException \Exception
     */
    public function testRenderLayoutWithMissingPartial()
    {
        $mock = $this->getMock('\stdClass', array());

        $this->contentHelper = $this->getContentHelper($mock);

        $this->contentHelper->renderLayout('MissinPartial');
    }

    /**
     * Test renderLayout with partial, with object call
     */
    public function testRenderLayoutWithPartial()
    {
        $mock = $this->getMock('\stdClass', array('getContent'));

        $mock->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('SomeContent'));

        $this->contentHelper = $this->getContentHelper($mock);

        $this->assertEquals('<p>SomeContent</p>', $this->contentHelper->renderLayout('OutputContent'));
    }

    /**
     * Test renderAttributes
     *
     * @dataProvider attributesProvider
     */
    public function testRenderAttributes($attrs, $expected)
    {
        $mock = $this->getMock('\stdClass', array());

        $this->contentHelper = $this->getContentHelper($mock);

        $this->assertEquals($expected, $this->contentHelper->renderAttributes($attrs));
    }

    /**
     * Provider for renderAttributes
     */
    public function attributesProvider()
    {
        return array(
            array(array('name' => 'bob', 'id' => 123, 'type' => 'test'), 'name="bob" id="123" type="test"'),
            array(array('name' => null, 'id' => 123, 'type' => 'test'), 'name="" id="123" type="test"'),
            array(array(), '')
        );
    }

    /**
     * Test replaceContent
     *
     * @dataProvider replaceContentProvider
     */
    public function testReplaceContent($content, $vars, $expected)
    {
        $mock = $this->getMock('\stdClass', array());

        $this->contentHelper = $this->getContentHelper($mock);

        $this->assertEquals($expected, $this->contentHelper->replaceContent($content, $vars));
    }

    /**
     * Data provider for replaceContent
     */
    public function replaceContentProvider()
    {
        return array(
            array('<p>No Variables</p>', array(), '<p>No Variables</p>'),
            array('<p>Foo {{bar}}</p>', array('bar' => 'BOB'), '<p>Foo BOB</p>'),
            array('<p>Foo {{bar}} {{cake}}</p>', array('bar' => 'BOB'), '<p>Foo BOB </p>'),
            array('{{[paragraph]}}', array('content' => 'FOO'), '<p>FOO</p>'),
            array('{{[paragraph]}}{{[paragraph]}}', array('content' => 'FOO'), '<p>FOO</p><p>FOO</p>')
        );
    }
}