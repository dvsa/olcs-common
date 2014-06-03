<?php

/**
 * Disk Store File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\File;

use Common\Service\File\DiskStoreFileUploader;

/**
 * Disk Store File Uploader Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DiskStoreFileUploaderTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    private $continue;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->config = array(
            'location' => __DIR__ . '/Resources/'
        );

        $this->continue = copy($this->config['location'] . 'TestFile.txt', $this->config['location'] . 'TmpFile.txt');

        if (!$this->continue) {
            $this->markTestSkipped('Unable to test file system');
            return;
        }
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        if (file_exists($this->config['location'] . 'TmpFile.txt')) {
            unlink($this->config['location'] . 'TmpFile.txt');
        }
    }

    /**
     * Test upload
     */
    public function testUpload()
    {
        $data = array(
            'name' => 'TestFile.txt',
            'type' => 'text/plain',
            'tmp_name' => $this->config['location'] . 'TmpFile.txt',
            'size' => 123
        );

        $uploader = $this->getMock('\Common\Service\File\DiskStoreFileUploader', array('moveFile'));
        $uploader->expects($this->once())
            ->method('moveFile')
            ->will(
                $this->returnCallback(
                    function ($oldPath, $newPath) {
                        return rename($oldPath, $newPath);
                    }
                )
            );

        $uploader->setConfig($this->config);
        $uploader->setFile($data);
        $uploader->upload();

        $id = $uploader->getFile()->getIdentifier();

        $response = $uploader->download($id, $data['name']);
        $this->assertInstanceOf('Zend\Http\Response\Stream', $response);

        $uploader->remove($id);

        $response = $uploader->download($id, $data['name']);
        $this->assertNotInstanceOf('Zend\Http\Response\Stream', $response);
        $this->assertInstanceOf('Zend\Http\Response', $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test upload with failure
     *
     * @expectedException \Exception
     */
    public function testUploadFail()
    {
        $data = array(
            'name' => 'TestFile.txt',
            'type' => 'text/plain',
            'tmp_name' => $this->config['location'] . 'TmpFile.txt',
            'size' => 123
        );

        $uploader = $this->getMock('\Common\Service\File\DiskStoreFileUploader', array('moveFile'));
        $uploader->expects($this->once())
            ->method('moveFile')
            ->will($this->returnValue(false));

        $uploader->setConfig($this->config);
        $uploader->setFile($data);
        $uploader->upload();
    }

    /**
     * Test move file
     */
    public function testMoveFile()
    {
        $uploader = new DiskStoreFileUploader();
        $this->assertFalse(
            $uploader->moveFile(
                $this->config['location'] . 'TestFile.txt',
                $this->config['location'] . 'TmpFile.txt'
            )
        );
    }
}
