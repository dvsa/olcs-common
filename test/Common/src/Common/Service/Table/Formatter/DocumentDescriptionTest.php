<?php

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
    protected $mockSm;

    protected $mockTranslator;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockTranslator = m::mock();

        $this->mockSm = m::mock()
            ->shouldReceive('get')
            ->with('translator')
            ->andReturn($this->mockTranslator)
            ->once()
            ->getMock();

    }

    public function testFormat()
    {
        // Params
        $data = [
            'documentStoreIdentifier' => 'olbs/tanres01/OLBS02/TanBsDocStore7/2014/08/foo.rtf',
            'description' => 'Foo file',
            'id' => 666,
        ];
        $column = [];

        // Mocks
        $mockUrlHelper = m::mock();

        // Expectations
        $this->mockSm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper);

        $mockUrlHelper->shouldReceive('fromRoute')
            ->with('getfile', ['identifier' => 666])
            ->andReturn('URL');

        $expected = '<a href="URL" >Foo file</a>';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $this->mockSm));
    }

    public function testFormatNoIdentifier()
    {
        // Params
        $data = [
            'description' => 'Foo file'
        ];
        $column = [];

        $expected = 'Foo file';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $this->mockSm));
    }

    public function testFormatEmptyIdentifier()
    {
        // Params
        $data = [
            'description' => 'Foo file',
            'document_store_id' => '',
        ];
        $column = [];

        $expected = 'Foo file';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $this->mockSm));
    }

    public function testFormatWithFilename()
    {
        $data = [
            'description' => null,
            'filename' => '/bar/cake/Foofile.txt'
        ];
        $column = [];

        $expected = 'Foofile.txt';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $this->mockSm));
    }

    public function testFormatWithNoDecriptionNoFilename()
    {
        $data = [
            'description' => null,
            'filename' => null
        ];
        $column = [];

        $this->mockTranslator
            ->shouldReceive('translate')
            ->with('internal.document-description.formatter.no-description')
            ->andReturn('File description missing')
            ->once()
            ->getMock();

        $expected = 'File description missing';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $this->mockSm));
    }

    public function testFormatWithHtml()
    {
        // Params
        $data = [
            'documentStoreIdentifier' => 'olbs/tanres01/OLBS02/TanBsDocStore7/2014/08/foo.html',
            'description' => 'Foo file',
            'id' => 666,
        ];
        $column = [];

        // Mocks
        $mockUrlHelper = m::mock();

        // Expectations
        $this->mockSm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper);

        $mockUrlHelper->shouldReceive('fromRoute')
            ->with('getfile', ['identifier' => 666])
            ->andReturn('URL');

        $expected = '<a href="URL" target="_blank">Foo file</a>';
        $this->assertEquals($expected, DocumentDescription::format($data, $column, $this->mockSm));
    }
}
