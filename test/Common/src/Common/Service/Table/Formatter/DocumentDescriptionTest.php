<?php

/**
 * Document Description Formatter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\DocumentDescription;

/**
 * Document Description Formatter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentDescriptionTest extends MockeryTestCase
{
    public function testFormat()
    {
        // Params
        $data = [
            'documentStoreIdentifier' => 'foo',
            'filename' => 'foo.pdf',
            'description' => 'Foo file'
        ];
        $column = [];

        // Mocks
        $sm = m::mock();
        $mockUrlHelper = m::mock();

        // Expectations
        $sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper);

        $mockUrlHelper->shouldReceive('fromRoute')
            ->with('getfile', ['file' => 'foo', 'name' => 'foo.pdf'])
            ->andReturn('URL');

        $expected = '<a href="URL" >Foo file</a>';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $sm));
    }

    public function testFormatWithHtml()
    {
        // Params
        $data = [
            'documentStoreIdentifier' => 'foo',
            'filename' => 'foo.html',
            'description' => 'Foo file'
        ];
        $column = [];

        // Mocks
        $sm = m::mock();
        $mockUrlHelper = m::mock();

        // Expectations
        $sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper);

        $mockUrlHelper->shouldReceive('fromRoute')
            ->with('getfile', ['file' => 'foo', 'name' => 'foo.html'])
            ->andReturn('URL');

        $expected = '<a href="URL" target="_blank">Foo file</a>';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $sm));
    }
}
