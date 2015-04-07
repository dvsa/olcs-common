<?php

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Helper;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Helper\DocumentGenerationHelperService;
use Mockery as m;

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentGenerationHelperServiceTest extends MockeryTestCase
{
    public function testGenerateFromTemplateWithEmptyQuery()
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('ContentStore')
            ->andReturn(
                m::mock()
                ->shouldReceive('read')
                ->with('/templates/x.rtf')
                ->andReturn('file')
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Document')
            ->andReturn(
                m::mock()
                ->shouldReceive('getBookmarkQueries')
                ->with('file', [])
                ->shouldReceive('populateBookmarks')
                ->with('file', [])
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerationHelperService();
        $helper->setServiceLocator($sm);

        $helper->generateFromTemplate('x');
    }

    public function testGenerateFromTemplateWithQuery()
    {
        $query = [
            'a' => 4,
            'b' => 10
        ];

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('ContentStore')
            ->andReturn(
                m::mock()
                ->shouldReceive('read')
                ->with('/templates/x.rtf')
                ->andReturn('file')
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Document')
            ->andReturn(
                m::mock()
                ->shouldReceive('getBookmarkQueries')
                ->with('file', ['y' => 1])
                ->andReturn($query)
                ->shouldReceive('populateBookmarks')
                ->with('file', ['c' => 5, 'd' => 50, 'z' => 2])
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Helper\Rest')
            ->andReturn(
                m::mock()
                ->shouldReceive('makeRestCall')
                ->with('BookmarkSearch', 'GET', [], $query)
                ->andReturn(['c' => 5, 'd' => 50])
                ->getMock()
            )

            ->getMock();

        $helper = new DocumentGenerationHelperService();
        $helper->setServiceLocator($sm);

        $helper->generateFromTemplate('x', ['y' => 1], ['z' => 2]);
    }

    public function testUploadGeneratedContent()
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('FileUploader')
            ->andReturn(
                m::mock()
                ->shouldReceive('getUploader')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setFile')
                    ->with(['content' => 'foo'])
                    ->shouldReceive('upload')
                    ->with('docs')
                    ->andReturn('result')
                    ->getMock()
                )
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerationHelperService();
        $helper->setServiceLocator($sm);

        $helper->uploadGeneratedContent('foo', 'docs', 'My File');
    }

    public function testGenerateAndStore()
    {
        /**
         * Mocking the SUT here is okay because this method is just a wrapper for
         * other public methods which are tested individually
         */
        $helper = m::mock('\Common\Service\Helper\DocumentGenerationHelperService')
            ->makePartial();

        $helper->shouldReceive('addTemplatePrefix')
            ->with([], 'template')
            ->andReturn('prefix-template')
            ->shouldReceive('generateFromTemplate')
            ->with('prefix-template', [], [])
            ->andReturn('content')
            ->shouldReceive('uploadGeneratedContent')
            ->with('content', 'documents', 'description')
            ->andReturn('result');

        $this->assertEquals('result', $helper->generateAndStore('template', 'description'));
    }

    public function testAddTemplatePrefixWithNoMatchingKey()
    {
        $helper = new DocumentGenerationHelperService();

        $this->assertEquals('template', $helper->addTemplatePrefix([], 'template'));
    }

    /**
     * @dataProvider templatePrefixProvider
     */
    public function testAddTemplatePrefixWithMatchingKey($niFlag, $prefix)
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('Entity\Licence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getOverview')
                ->with(123)
                ->andReturn(
                    [
                        'niFlag' => $niFlag
                    ]
                )
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerationHelperService();
        $helper->setServiceLocator($sm);

        $this->assertEquals(
            $prefix . '/template',
            $helper->addTemplatePrefix(['licence' => 123], 'template')
        );
    }

    public function templatePrefixProvider()
    {
        return [
            ['N', 'GB'],
            ['Y', 'NI']
        ];
    }
}
