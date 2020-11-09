<?php

/**
 * Test EscapeHtml view helper
 *
 * @author Andy Newton <andy@vitri.ltd>
 */

namespace CommonTest\View\Helper;

use Common\View\Helper\EscapeHtml;
use HTMLPurifier;
use Mockery as m;

class EscapeHtmlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test Escape HTML helper
     */
    public function testEscapeHtml()
    {
        $mockHtmlPurifier = m::mock(HtmlPurifier::class);
        $mockHtmlPurifier->shouldReceive('purify')
            ->once()
            ->with('<badtag>foo</badtag>')
            ->andReturn('foo');

        $viewHelper = new EscapeHtml($mockHtmlPurifier);

        $this->assertEquals('foo', $viewHelper->__invoke('<badtag>foo</badtag>'));
    }
}
