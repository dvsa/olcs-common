<?php
/**
 * Test FlashMessengerTrait
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */

namespace CommonTest\Controller\Util;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ResponseHelperTest extends AbstractHttpControllerTestCase
{
    
    public $handleReponseMethods = array(
        'checkForValidResponseBody',
        'checkForInternalServerError',
        'checkForUnexpectedResponseCode'
    );
    
    public function testSetResponse()
    {
        $mock = $this->getMock('\Common\Util\ResponseHelper', array('blah'));
        $response = new \Zend\Http\Response;
        $mock->setResponse($response);
    }
    
    public function testGetResponse()
    {
        $mock = $this->getMock('\Common\Util\ResponseHelper', array('blah'));
        $mock->response = new \Zend\Http\Response;
        $return = $mock->getResponse();
    }
    
    public function testSetMethod()
    {
        $mock = $this->getMock('\Common\Util\ResponseHelper', array('blah'));
        $return = $mock->setMethod('blah');
    }
    
    public function testSetParams()
    {
        $mock = $this->getMock('\Common\Util\ResponseHelper', array('blah'));
        $return = $mock->setParams(array(1,2,3));
    }
    
    public function testSetData()
    {
        $mock = $this->getMock('\Common\Util\ResponseHelper', array('blah'));
        $return = $mock->getData(array(1,2,3));
    }
    
    public function getSutMock($methods)
    {
        return $this->getMock(
            '\Common\Util\ResponseHelper',
            $methods
        );
    }
    
    public function testHandleResponseGet()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'GET';
        $return = $mock->handleResponse();
    }
    
    public function testHandleInvalidResponseGet()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(404));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'GET';
        $return = $mock->handleResponse();
    }
    
    public function testHandleResponsePost()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(201));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'POST';
        $return = $mock->handleResponse();
    }
    
    public function testHandleInvalidResponsePost()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(404));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'POST';
        $return = $mock->handleResponse();
    }
    
    public function testHandleResponsePut()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'PUT';
        $return = $mock->handleResponse();
    }
    
    public function testHandleInvalidResponsePut()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(404));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'PUT';
        $return = $mock->handleResponse();
    }
    
    public function testHandleResponseDelete()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'DELETE';
        $return = $mock->handleResponse();
    }
    
    public function testHandleInvalidResponseDelete()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);
        
        $response = $this->getMock('\stdClass', array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(404));
        $mock->response = $response;
        
        $mock->expects($this->once())
            ->method('checkForValidResponseBody')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        
        $mock->expects($this->once())
            ->method('checkForInternalServerError')
            ->with('{}');
        $mock->method = 'DELETE';
        $return = $mock->handleResponse();
    }
    
    public function testCheckForValidResponseBody()
    {
        $mock = $this->getSutMock(array('blah'));
        $return = $mock->checkForValidResponseBody('{}');
    }
    
    /**
     * @expectedException Exception
     */
    public function testCheckForInvalidResponseBodyString()
    {
        $mock = $this->getSutMock(array('blah'));
        $return = $mock->checkForValidResponseBody(55);
    }
    
    /**
     * @expectedException Exception
     */
    public function testCheckForInvalidResponseBodyJson()
    {
        $mock = $this->getSutMock(array('blah'));
        $return = $mock->checkForValidResponseBody('blah');
    }
    
    /**
     * @expectedException Exception
     */
    public function testCheckForInternalServerError()
    {
        $mock = $this->getSutMock(array('blah'));
        $response = $this->getMock('\stdClass', array('getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(500));
        $mock->response = $response;
        $return = $mock->checkForInternalServerError('{}');
    }
    
    /**
     * @expectedException Exception
     */
    public function testCheckForUnexpectedResponseCode()
    {
        $mock = $this->getSutMock(array('blah'));
        $response = $this->getMock('\stdClass', array('getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(500));
        $mock->response = $response;
        $mock->method = 'GET';
        $return = $mock->checkForUnexpectedResponseCode('{}');
    }
}
