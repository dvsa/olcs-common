<?php

declare(strict_types=1);

namespace CommonTest\View\Helper;

use Common\View\Helper\LinkNewWindow;
use Laminas\View\Renderer\RendererInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @see LinkNewWindow
 */
class LinkNewWindowTest extends MockeryTestCase
{
    /**
     * @dataProvider dpIsExternalLink
     */
    public function testInvoke($isExternal, $output): void
    {
        $linkText = 'link text';
        $translatedLinkText = 'translated link text';
        $translatedLinkTextEscaped = 'escaped translated link text';
        $screenReaderText = 'screen reader text';
        $translatedScreenReaderText = 'translated screen reader text';
        $translatedScreenReaderTextEscaped = 'escaped translated screen reader text';
        $linkClass = 'link class';
        $linkClassEscaped = 'escaped link class';
        $url = 'http://url';
        $urlEscaped = 'http://url/escaped';

        $view = m::mock(RendererInterface::class);
        $view->expects('translate')
            ->with($linkText)
            ->andReturn($translatedLinkText);

        $view->expects('escapeHtml')
            ->with($translatedLinkText)
            ->andReturn($translatedLinkTextEscaped);

        $view->expects('translate')
            ->with($screenReaderText)
            ->andReturn($translatedScreenReaderText);

        $view->expects('escapeHtml')
            ->with($translatedScreenReaderText)
            ->andReturn($translatedScreenReaderTextEscaped);

        $view->expects('escapeHtmlAttr')
            ->with($url)
            ->andReturn($urlEscaped);

        $view->expects('escapeHtmlAttr')
            ->with($linkClass)
            ->andReturn($linkClassEscaped);

        $sut = new LinkNewWindow();
        $sut->setView($view);

        self::assertEquals($output, $sut->__invoke($url, $linkText, $linkClass, $screenReaderText, $isExternal));
    }

    public function dpIsExternalLink(): array
    {
        $internalLinkOutput = '<a href="http://url/escaped" class="escaped link class" target="_blank">escaped translated link text<span class="govuk-visually-hidden">escaped translated screen reader text</span></a>';
        $externalLinkOutput = '<a href="http://url/escaped" class="escaped link class" target="_blank" rel="external noreferrer noopener">escaped translated link text<span class="govuk-visually-hidden">escaped translated screen reader text</span></a>';

        return [
            [false, $internalLinkOutput],
            [true, $externalLinkOutput],
        ];
    }
}
