<?php

/**
 * Content Store File Uploader Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\File;

use Common\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use PHPUnit_Framework_TestCase;

/**
 * Content Store File Uploader Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ContentStoreFileUploaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->uploader = new ContentStoreFileUploader();

        $this->contentStoreMock = $this->getMock(
            '\stdClass',
            ['remove', 'read', 'write']
        );

        $sl = $this->getMock(
            '\Zend\ServiceManager\ServiceLocatorInterface',
            ['get', 'has']
        );

        $sl->expects($this->any())
            ->method('get')
            ->with('ContentStore')
            ->will($this->returnValue($this->contentStoreMock));

        $this->uploader->setConfig(
            ['location' => 'test']
        );

        $this->uploader->setServiceLocator($sl);
    }

    public function testDownloadWithNoFileFound()
    {
        $response = $this->uploader->download('identifier', 'file.txt');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('File not found', $response->getContent());
    }

    public function testDownloadWithValidFile()
    {
        $file = new File();
        $file->setContent('dummy content');

        $this->contentStoreMock->expects($this->once())
            ->method('read')
            ->with('test/identifier')
            ->will($this->returnValue($file));

        $response = $this->uploader->download('identifier', 'file.txt');

        $headers = [
            'Content-Disposition' => 'attachment; filename="file.txt"',
            'Content-Length' => '13',
            'Content-Type' => 'text/plain'
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('dummy content', $response->getContent());
        $this->assertEquals($headers, $response->getHeaders()->toArray());
    }

    public function testDownloadWithValidHtmlFile()
    {
        $file = new File();
        $file->setContent('dummy content');

        $this->contentStoreMock->expects($this->once())
            ->method('read')
            ->with('test/identifier')
            ->will($this->returnValue($file));

        $response = $this->uploader->download('identifier', 'file.html');

        $headers = [
            'Content-Length' => '13',
            'Content-Type' => 'text/plain'
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('dummy content', $response->getContent());
        $this->assertEquals($headers, $response->getHeaders()->toArray());
    }

    public function testDownloadWithValidFileAndNamespace()
    {
        $file = new File();
        $file->setContent('dummy content');

        $this->contentStoreMock->expects($this->once())
            ->method('read')
            ->with('foo/identifier')
            ->will($this->returnValue($file));

        $this->uploader->download('identifier', 'file.txt', 'foo');
    }

    public function testRemoveProxiesThroughToContentStore()
    {
        $this->contentStoreMock->expects($this->once())
            ->method('remove')
            ->with('test/identifier');

        $this->uploader->remove('identifier');
    }

    public function testUploadWithFileWithContent()
    {
        $response = $this->getMock('Zend\Http\Response');
        $response->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->contentStoreMock->expects($this->once())
            ->method('write')
            ->willReturn($response);

        $this->uploader->setFile(
            [
                'content' => 'dummy content'
            ]
        );

        $this->uploader->upload('documents');
    }

    public function testUploadWithFileWithPath()
    {
        $response = $this->getMock('Zend\Http\Response');
        $response->expects($this->once())
            ->method('isSuccess')
            ->willReturn(true);

        $this->contentStoreMock->expects($this->once())
            ->method('write')
            ->willReturn($response);

        $this->uploader->setFile(
            [
                'tmp_name' => __DIR__ . '/Resources/TestFile.txt'
            ]
        );

        $file = $this->uploader->upload('documents');

        $this->assertEquals(
            "Don't modify this file",
            $file->getContent()
        );
    }

    /**
     * @expectedException        \Common\Service\File\Exception
     * @expectedExceptionMessage Unable to store uploaded file
     */
    public function testUploadWithErrorResponse()
    {
        $response = $this->getMock('Zend\Http\Response');
        $response->expects($this->once())
            ->method('isSuccess')
            ->willReturn(false);

        $this->contentStoreMock->expects($this->once())
            ->method('write')
            ->willReturn($response);

        $this->uploader->setFile(
            [
                'content' => 'dummy content'
            ]
        );

        $this->uploader->upload('documents');
    }
}
