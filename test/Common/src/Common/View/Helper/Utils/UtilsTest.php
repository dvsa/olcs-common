<?php

namespace CommonTest\View\Helper\Utils;

use Common\View\Helper\Traits\Utils;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Helper\AbstractHelper;
use Zend\View\View;

/**
 * @covers Common\View\Helper\Traits\Utils
 */
class UtilsTest extends MockeryTestCase
{
    public function testFromPluginsMngr()
    {
        $mockView = m::mock(TestView::class);
        $mockView->shouldReceive('plugin')
            ->once()
            ->with('escapehtml')
            ->andReturn('not_EscapeHtml_Instance');

        /** @var m\MockInterface|TestHelper $sut */
        $sut = m::mock(TestHelper::class)->makePartial('getView');
        $sut->shouldReceive('getView')
            ->once()    //  must be called only once
            ->andReturn($mockView);

        //  call & check
        static::assertEquals('text &amp;amp;', $sut->escapeHtml('text &amp;'));
        //  call again, to check we not define plugin again $sut-getView()->ONCE()
        static::assertSame('text2', $sut->escapeHtml('text2'));
    }
}

/**
 * Helper Dummy class for testing trait
 */
class TestHelper extends AbstractHelper
{
    use Utils;
}

/**
 * View Dummy class for testing
 */
class TestView extends View
{
    public function plugin()
    {
    }
}
