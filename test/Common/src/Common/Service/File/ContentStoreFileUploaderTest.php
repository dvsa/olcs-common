<?php

/**
 * Content Store File Uploader Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\File;

use Common\Service\File\ContentStoreFileUploader;

/**
 * Content Store File Uploader Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ContentStoreFileUploaderTest extends \PHPUnit_Framework_TestCase
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
        $file = new \Dvsa\Jackrabbit\Data\Object\File();
        $file->setContent('dummy content');

        $this->contentStoreMock->expects($this->once())
            ->method('read')
            ->with('identifier')
            ->will($this->returnValue($file));

        $response = $this->uploader->download('identifier', 'file.txt');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('dummy content', $response->getContent());
    }

    public function testRemoveProxiesThroughToContentStore()
    {
        $this->contentStoreMock->expects($this->once())
            ->method('remove')
            ->with('identifier');

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
                'content' => 'dummy content',
                'type' => 'txt/plain'
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
                'tmp_name' => __DIR__ . '/Resources/TestFile.txt',
                'type' => 'txt/plain'
            ]
        );

        $file = $this->uploader->upload('documents');

        $this->assertEquals(
            "Don't modify this file",
            $file->getContent()
        );
    }

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
                'content' => 'dummy content',
                'type' => 'txt/plain'
            ]
        );

        try {
            $this->uploader->upload('documents');
        } catch (\Exception $e) {
            $this->assertEquals('Unable to store uploaded file', $e->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }
}
