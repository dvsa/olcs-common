<?php

declare(strict_types=1);

namespace CommonTest\View\Helper;

use Common\View\Helper\LinkNewWindowExternal;
use Laminas\View\Renderer\RendererInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see LinkNewWindowExternal
 */
class LinkNewWindowExternalTest extends MockeryTestCase
{
    public function testInvoke(): void
    {
        $linkText = 'link text';
        $screenReaderText = 'screen reader text';
        $linkClass = 'link class';
        $url = 'http://url';
        $output = 'output';

        $view = m::mock(RendererInterface::class);
        $view->expects('linkNewWindow')
            ->with($url, $linkText, $linkClass, $screenReaderText, true)
            ->andReturn($output);

        $sut = new LinkNewWindowExternal();
        $sut->setView($view);

        self::assertEquals($output, $sut->__invoke($url, $linkText, $linkClass, $screenReaderText));
    }
}
