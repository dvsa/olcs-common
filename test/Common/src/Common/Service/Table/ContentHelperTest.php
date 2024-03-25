<?php

declare(strict_types=1);

namespace CommonTest\Service\Table;

use Common\Service\Table\ContentHelper;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;
use Laminas\Http\Response;

class ContentHelperTest extends TestCase
{
    /**
     * Setup the content helper
     */
    public function getContentHelper($mock)
    {
        return new ContentHelper(__DIR__ . '/TestResources', $mock);
    }

    /**
     * Test translator set correctly
     */
    public function testTranslatorSet(): void
    {
        $translatorMock = $this->createMock(\Laminas\Mvc\I18n\Translator::class);

        $mock = $this->createPartialMock(ContentHelper::class, ['getTranslator']);

        $mock->expects($this->once())
            ->method('getTranslator')
            ->willReturn($translatorMock);

        $this->assertSame($translatorMock, $this->getContentHelper($mock)->getTranslator());
    }

    /**
     * Test renderLayout with missing partial
     *
     */
    public function testRenderLayoutWithMissingPartial(): void
    {
        $this->expectException(\Exception::class);

        $contentHelper = $this->getContentHelper(null);

        $contentHelper->renderLayout('MissinPartial');
    }

    /**
     * Test renderLayout with partial, with object call
     */
    public function testRenderLayoutWithPartial(): void
    {
        $mock = $this->createMock(Response::class);

        $mock->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('SomeContent'));

        $contentHelper = $this->getContentHelper($mock);

        $this->assertEquals('<p>SomeContent</p>', $contentHelper->renderLayout('OutputContent'));
    }

    /**
     * Test renderAttributes
     *
     * @dataProvider attributesProvider
     */
    public function testRenderAttributes($attrs, $expected): void
    {
        $contentHelper = $this->getContentHelper(null);

        $this->assertEquals($expected, $contentHelper->renderAttributes($attrs));
    }

    /**
     * Provider for renderAttributes
     */
    public function attributesProvider()
    {
        return [
            [['name' => 'bob', 'id' => 123, 'type' => 'test'], 'name="bob" id="123" type="test"'],
            [['name' => null, 'id' => 123, 'type' => 'test'], 'name="" id="123" type="test"'],
            [[], '']
        ];
    }

    /**
     * Test replaceContent
     *
     * @dataProvider replaceContentProvider
     */
    public function testReplaceContent($content, $vars, $expected): void
    {
        $contentHelper = $this->getContentHelper(null);

        $this->assertEquals($expected, $contentHelper->replaceContent($content, $vars));
    }

    /**
     * Data provider for replaceContent
     */
    public function replaceContentProvider()
    {
        return [
            ['<p>No Variables</p>', [], '<p>No Variables</p>'],
            ['<p>Foo {{bar}}</p>', ['bar' => 'BOB'], '<p>Foo BOB</p>'],
            ['<p>Foo {{bar}} {{cake}}</p>', ['bar' => 'BOB'], '<p>Foo BOB </p>'],
            ['{{[paragraph]}}', ['content' => 'FOO'], '<p>FOO</p>'],
            ['{{[paragraph]}}{{[paragraph]}}', ['content' => 'FOO'], '<p>FOO</p><p>FOO</p>']
        ];
    }
}
