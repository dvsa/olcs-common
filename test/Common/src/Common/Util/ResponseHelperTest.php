<?php

/**
 * Test ResponseHelperTest
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
namespace CommonTest\Controller\Util;

use Laminas\Http\Response as HttpResponse;

/**
 * Test ResponseHelperTest
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 */
class ResponseHelperTest extends \PHPUnit\Framework\TestCase
{

    public $handleReponseMethods = array(
        'checkForValidResponseBody',
        'checkForInternalServerError',
        'checkForUnexpectedResponseCode'
    );

    public function getSutMock($methods = [])
    {
        if ($methods === null) {
            $methods = [];
        }

        return $this->createPartialMock(
            '\Common\Util\ResponseHelper',
            $methods
        );
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetResponse()
    {
        $mock = $this->createMock('\Common\Util\ResponseHelper', null);
        $response = new \Laminas\Http\Response;
        $mock->setResponse($response);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetResponse()
    {
        $mock = $this->createMock('\Common\Util\ResponseHelper', null);
        $mock->response = new \Laminas\Http\Response;
        $mock->getResponse();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetMethod()
    {
        $mock = $this->createMock('\Common\Util\ResponseHelper', null);
        $mock->setMethod('blah');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetParams()
    {
        $mock = $this->createMock('\Common\Util\ResponseHelper', null);
        $mock->setParams(array(1, 2, 3));
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSetData()
    {
        $mock = $this->createMock('\Common\Util\ResponseHelper', null);
        $mock->getData(array(1, 2, 3));
    }

    public function testHandleResponseGet()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleInvalidResponseGet()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleResponsePost()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleInvalidResponsePost()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleResponsePut()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleInvalidResponsePut()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleResponseDelete()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleInvalidResponseDelete()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
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
        $mock->handleResponse();
    }

    public function testHandleInvalidResponseMethod()
    {
        $mock = $this->getSutMock($this->handleReponseMethods);

        $response = $this->createPartialMock(HttpResponse::class, array('getBody', 'getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getBody')
            ->will($this->returnValue('{}'));
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
        $mock->method = 'BLAH';
        $mock->handleResponse();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testCheckForValidResponseBody()
    {
        $mock = $this->getSutMock([]);
        $mock->checkForValidResponseBody('{}');
    }

    public function testCheckForInvalidResponseBodyString()
    {
        $this->expectException(\Exception::class);

        $mock = $this->getSutMock(null);
        $mock->checkForValidResponseBody(55);
    }

    public function testCheckForInvalidResponseBodyJson()
    {
        $this->expectException(\Exception::class);

        $mock = $this->getSutMock(null);
        $mock->checkForValidResponseBody('blah');
    }

    public function testCheckForInternalServerError()
    {
        $this->expectException(\Exception::class);

        $mock = $this->getSutMock(null);
        $response = $this->createPartialMock(HttpResponse::class, array('getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(500));
        $mock->response = $response;
        $mock->checkForInternalServerError('{}');
    }

    public function testCheckForNoInternalServerError()
    {
        $mock = $this->getSutMock(null);
        $response = $this->createPartialMock(HttpResponse::class, array('getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $mock->response = $response;
        $mock->checkForInternalServerError('{}');
    }

    public function testCheckForUnexpectedResponseCode()
    {
        $this->expectException(\Exception::class);

        $mock = $this->getSutMock(null);
        $response = $this->createPartialMock(HttpResponse::class, array('getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(500));
        $mock->response = $response;
        $mock->method = 'GET';
        $mock->checkForUnexpectedResponseCode('{}');
    }

    public function testCheckForExpectedResponseCode()
    {
        $mock = $this->getSutMock(null);
        $response = $this->createPartialMock(HttpResponse::class, array('getStatusCode'));
        $response->expects($this->atLeastOnce())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $mock->response = $response;
        $mock->method = 'GET';
        $mock->checkForUnexpectedResponseCode('{}');
    }
}
