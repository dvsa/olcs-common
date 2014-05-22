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
    
    public function testHandleResponse() 
    {
        $mock = $this->getMock('\Common\Util\ResponseHelper', array('blah'));
        //print_r(get_class_methods($mock));
        $response = $this->getMock('\stdClass', array('getBody'));
        $mock->response = $response;
        //print_r($mock);
        $return = $mock->handleResponse();
    }
}
