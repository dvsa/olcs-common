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

    public function testUploadWithContentStoreFile()
    {
        $this->markTestIncomplete();
    }

    public function testUploadWithTmpFile()
    {
        $this->markTestIncomplete();
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
}
