<?php
/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */

namespace CommonTest\Controller\Util;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RestCallTraitTest extends AbstractHttpControllerTestCase
{
    public $class = '\Common\Util\RestCallTrait';
    
    public function getSutMock($class = null, $methods = null)
    {
        $class = !empty($class) ? $class : $this->class;
        return $this->getMockForTrait($class, array(), '', true, true, true, $methods);
    }
    
    public function testSendGet()
    {
        $mock = $this->getSutMock(null, array('getRestClient'));
        $restClient = $this->getMock('\stdClass', array('get'));
        $restClient->expects($this->once())
            ->method('get')
            ->with('', array('id' => 1))
            ->will($this->returnValue('restClient'));
        
        $mock->expects($this->once())
            ->method('getRestClient')
            ->with('Licence')
            ->will($this->returnValue($restClient));
        $mock->sendGet('Licence', array('id' => 1));
    }
    
    public function testSendPost()
    {
        $mock = $this->getSutMock(null, array('getRestClient'));
        $restClient = $this->getMock('\stdClass', array('post'));
        $restClient->expects($this->once())
            ->method('post')
            ->with('', array('id' => 1))
            ->will($this->returnValue('restClient'));
        
        $mock->expects($this->once())
            ->method('getRestClient')
            ->with('Licence')
            ->will($this->returnValue($restClient));
        $mock->sendPost('Licence', array('id' => 1));
    }
    
    public function getRestClientAndResponseHandlerMocks($mock, $method, $reponseHandler, $data, $path = '')
    {
        $mock->expects($this->once())
            ->method('getServiceRestClient')
            ->with('Licence', $method, $path, $data)
            ->will($this->returnValue('restClient'));
        
        $mock->expects($this->once())
            ->method('handleResponseMethod')
            ->with($reponseHandler, 'Licence', 'restClient')
            ->will($this->returnValue('response'));
        
        return $mock;
    }
    
    public function testMakeRestCallGet()
    {
        $mock = $this->getSutMock(null, array('getServiceRestClient', 'handleResponseMethod'));
        $mock = $this->getRestClientAndResponseHandlerMocks($mock, 'get', 'handleGetResponse', array('id' => 1, 'bundle' =>'{"licence":[]}'));
        $mock->makeRestCall('Licence', 'GET', array('id' => 1), array('licence' => array()));
    }
    
    public function testMakeRestCallGetList()
    {
        $mock = $this->getSutMock(null, array('getServiceRestClient', 'handleResponseMethod'));
        $mock = $this->getRestClientAndResponseHandlerMocks($mock, 'get', 'handleGetListResponse', array());
        $mock->makeRestCall('Licence', 'GET', array(), null);
    }
    
    public function testMakeRestCallPost()
    {
        $mock = $this->getSutMock(null, array('getServiceRestClient', 'handleResponseMethod'));
        $mock = $this->getRestClientAndResponseHandlerMocks($mock, 'post', 'handlePostResponse', array('data' => '{"id":1}'));
        $mock->makeRestCall('Licence', 'POST', array('id' => 1), null);
    }
    
    public function testMakeRestCallPut()
    {
        $mock = $this->getSutMock(null, array('getServiceRestClient', 'handleResponseMethod'));
        $mock = $this->getRestClientAndResponseHandlerMocks($mock, 'put', 'handlePutResponse', array('data' => '[]'), '/1');
        $mock->makeRestCall('Licence', 'PUT', array('id' => 1), null);
    }
    
    public function testMakeRestCallDelete()
    {
        $mock = $this->getSutMock(null, array('getServiceRestClient', 'handleResponseMethod'));
        $mock = $this->getRestClientAndResponseHandlerMocks($mock, 'delete', 'handleDeleteResponse', array('id' => 1, 'bundle' =>'{"licence":[]}'));
        $mock->makeRestCall('Licence', 'DELETE', array('id' => 1), array('licence' => array()));
    }
    
    public function testMakeRestCallInvalid()
    {
        $mock = $this->getSutMock(null, array('getServiceRestClient', 'handleResponseMethod'));
        $mock->makeRestCall('Licence', 'BLAH', array('id' => 1), array('licence' => array()));
    }
    
    public function testGetRestClient()
    {
        $mock = $this->getSutMock(null, array('getServiceLocator'));
        
        $serviceLocator = $this->getMock('\stdClass', array('get'));
        $apiResolver = $this->getMock('\stdClass', array('getClient'));
        
        $apiResolver->expects($this->once())
            ->method('getClient')
            ->with('Licence')
            ->will($this->returnValue(true));
        
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('ServiceApiResolver')
            ->will($this->returnValue($apiResolver));
        
        $mock->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocator));
        
        $mock->getRestClient('Licence');
    }
    
    public function testHandleGetResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handleGetResponse('Licence', 'response');
    }
    
    public function testHandleGetListResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handleGetListResponse('Licence', 'response');
    }
    
    /**
     * @expectedException Exception
     */
    public function testHandleFailedGetListResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handleGetListResponse('Licence', false);
    }
    
    public function testHandlePostResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handlePostResponse('Licence', 'response');
    }
    
    /**
     * @expectedException Exception
     */
    public function testHandleFailedPostResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handlePostResponse('Licence', false);
    }
    
    public function testHandlePutResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handlePutResponse('Licence', 'response');
    }
    
     /**
     * @expectedException Exception
     */
    public function testHandlePutResponse400()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handlePutResponse('Licence', 400);
    }
    
    /**
     * @expectedException Exception
     */
    public function testHandlePutResponse404()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handlePutResponse('Licence', 404);
    }
    
    /**
     * @expectedException Exception
     */
    public function testHandlePutResponse409()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handlePutResponse('Licence', 409);
    }
    
    public function testHandleDeleteResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handleDeleteResponse('Licence', 'response');
    }
    
    /**
     * @expectedException Exception
     */
    public function testHandleFailedDeleteResponse()
    {
        $mock = $this->getSutMock(null, null);
        $mock->handleDeleteResponse('Licence', false);
    }
    
    public function testGetDoctrineHydrator()
    {
        //$this->serviceManager = \OlcsTest\Bootstrap::getServiceManager();
        //$em = $this->serviceManager->get('doctrine.entitymanager.orm_default');
        /*$mock = $this->getSutMock(null, array('getServiceLocator'));
        $this->doctrineHydrator = 'DoctrineHydrator';
        
        $serviceLocator = $this->getMock('\stdClass', array('get'));
        
         $serviceLocator->expects($this->once())
            ->method('get')
            ->with('doctrine.entitymanager.orm_default')
            ->will($this->returnValue('doctrine.em'));
        
        $mock->expects($this->once())
            ->method('getServiceLocator')
            ->will($this->returnValue($serviceLocator));
        
        $mock->getDoctrineHydrator();*/
    }
}
