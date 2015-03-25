<?php

/**
 * Test FileControllerTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller;

use Common\Controller\FileController;

/**
 * Test FileControllerTest
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FileControllerTest extends \PHPUnit_Framework_TestCase
{
    private $file;
    private $name;
    private $response;

    public function testDownloadAction()
    {
        $this->file = 'ksdhglkagljksdfg';
        $this->name = 'SomeFile.png';

        $mockServiceLocator = $this->getMockServiceLocator();
        $mockPluginManager = $this->getMockPluginManager();

        $controller = new FileController();
        $controller->setServiceLocator($mockServiceLocator);
        $controller->setPluginManager($mockPluginManager);

        $this->assertSame($this->getMockResponse(), $controller->downloadAction());
    }

    private function getMockResponse()
    {
        if (empty($this->response)) {
            $this->response = $this->getMock('Zend\Http\Response');
        }
        return $this->response;
    }

    private function getMockFileUploaderService()
    {
        $mockFileUploaderService = $this->getMock('\stdClass', array('getUploader'));
        $mockFileUploaderService->expects($this->once())
            ->method('getUploader')
            ->will($this->returnValue($this->getMockFileUploader()));

        return $mockFileUploaderService;
    }

    private function getMockFileUploader()
    {
        $mockFileUploader = $this->getMock('\stdClass', array('download'));
        $mockFileUploader->expects($this->once())
            ->method('download')
            ->with($this->file, $this->name)
            ->will($this->returnValue($this->getMockResponse()));

        return $mockFileUploader;
    }

    private function getMockServiceLocator()
    {
        $mockServiceLocator = $this->getMock('Zend\ServiceManager\ServiceManager', array('get'));
        $mockServiceLocator->expects($this->any())
            ->method('get')
            ->with('FileUploader')
            ->will($this->returnValue($this->getMockFileUploaderService()));

        return $mockServiceLocator;
    }

    private function getMockPluginManager()
    {
        $mockPluginManager = $this->getMock('Zend\Mvc\Controller\PluginManager', array('get'));
        $mockPluginManager->expects($this->any())
            ->method('get')
            ->with('params')
            ->will($this->returnValue($this->getMockParams()));

        return $mockPluginManager;
    }

    private function getMockParams()
    {
        $mockParams = $this->getMock('\stdClass', array('fromRoute', 'fromQuery'));
        $mockParams->expects($this->any())
            ->method('fromRoute')
            ->will(
                $this->returnValueMap(
                    array(
                        array('file', $this->file),
                        array('name', $this->name)
                    )
                )
            );

        $mockParams->expects($this->any())
            ->method('fromQuery')
            ->will(
                $this->returnValueMap(
                    array(
                        array('inline', false)
                    )
                )
            );

        return $mockParams;
    }
}
