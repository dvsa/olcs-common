<?php

/**
 * FileUploaderFactoryTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\File;

use Common\Service\File\FileUploaderFactory;

/**
 * FileUploaderFactoryTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileUploaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {
        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager');

        $factory = new FileUploaderFactory();
        $this->assertSame($factory, $factory->createService($mockServiceLocator));
    }

    public function testGetUploader()
    {
        $config = array(
            'file_uploader' => array(
                'default' => 'DiskStore',
                'config' => array(

                )
            )
        );

        $mockServiceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get'));
        $mockServiceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue($config));

        $factory = new FileUploaderFactory();
        $this->assertSame($factory, $factory->createService($mockServiceLocator));

        $uploader = $factory->getUploader();

        $this->assertInstanceOf('\Common\Service\File\DiskStoreFileUploader', $uploader);
    }
}
