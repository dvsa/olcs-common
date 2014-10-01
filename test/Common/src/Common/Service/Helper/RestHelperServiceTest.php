<?php

/**
 * Rest Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Common\Service\Helper\RestHelperService;

/**
 * Rest Helper Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RestHelperServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\RestHelperService
     */
    private $sut;

    private $serviceManager;

    private $mockApiResolver;

    private $mockService;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->mockApiResolver = $this->getMock('\stdClass', array('getClient'));
        $this->mockApiResolver->expects($this->any())
            ->method('getClient')
            ->will($this->returnCallback(array($this, 'getClient')));

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('ServiceApiResolver', $this->mockApiResolver);

        $this->sut = new RestHelperService();
        $this->sut->setServiceLocator($this->serviceManager);
    }

    /**
     * Mock get client
     *
     * @param string $service
     */
    public function getClient($service)
    {
        if ($this->mockService === null) {
            $client = new \stdClass();
            $client->service = $service;

            return $client;
        }

        return $this->mockService;
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testGetRestClient()
    {
        $service = 'Sample';

        $restClient = $this->sut->getRestClient($service);

        $this->assertEquals($service, $restClient->service);

        $restClient2 = $this->sut->getRestClient($service);

        $this->assertEquals($service, $restClient2->service);

        $this->assertNotSame($restClient, $restClient2);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testSendGet()
    {
        $service = 'Sample';
        $data = array(
            'id' => 1
        );
        $response = array(
            'foo' => 'bar'
        );

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->sendGet($service, $data));
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testSendGetWithAppend()
    {
        $service = 'Sample';
        $data = array(
            'id' => 1
        );
        $response = array(
            'foo' => 'bar'
        );

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('/1', array())
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->sendGet($service, $data, true));
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testSendPost()
    {
        $service = 'Sample';
        $data = array(
            'id' => 1
        );
        $response = array(
            'foo' => 'bar'
        );

        $this->mockService = $this->getMock('\stdClass', array('post'));
        $this->mockService->expects($this->once())
            ->method('post')
            ->with('', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($response, $this->sut->sendPost($service, $data));
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallGet()
    {
        $service = 'Sample';
        $method = 'GET';
        $data = array('id' => 1);
        $bundle = null;
        $response = array('foo' => 'bar');

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('', $data)
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data, $bundle);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallGetWithIdData()
    {
        $service = 'Sample';
        $method = 'GET';
        $data = 1;
        $bundle = null;
        $response = array('foo' => 'bar');

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('', array('id' => $data))
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data, $bundle);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallGetWithIdDataWithBundle()
    {
        $service = 'Sample';
        $method = 'GET';
        $data = 1;
        $bundle = array('fudge' => 'cake');
        $response = array('foo' => 'bar');

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('', array('id' => $data, 'bundle' => '{"fudge":"cake"}'))
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data, $bundle);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallGetList()
    {
        $service = 'Sample';
        $method = 'GET';
        $data = array('foo' => 'bar');
        $bundle = null;
        $response = array('foo' => 'bar');

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('', $data)
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data, $bundle);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallGetListWithBundle()
    {
        $service = 'Sample';
        $method = 'GET';
        $data = array('foo' => 'bar');
        $bundle = array('fudge' => 'cake');
        $response = array('foo' => 'bar');
        $expectedData = array('foo' => 'bar', 'bundle' => '{"fudge":"cake"}');

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('', $expectedData)
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data, $bundle);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testMakeRestCallGetListWithNoResults()
    {
        $service = 'Sample';
        $method = 'GET';
        $data = array('foo' => 'bar');
        $bundle = null;
        $response = false;

        $this->mockService = $this->getMock('\stdClass', array('get'));
        $this->mockService->expects($this->once())
            ->method('get')
            ->with('', $data)
            ->will($this->returnValue($response));

        $this->sut->makeRestCall($service, $method, $data, $bundle);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallPost()
    {
        $service = 'Sample';
        $method = 'POST';
        $data = array('foo' => 'bar');
        $response = array('id' => 1);
        $expectedData = array(
            'data' => '{"foo":"bar"}'
        );

        $this->mockService = $this->getMock('\stdClass', array('post'));
        $this->mockService->expects($this->once())
            ->method('post')
            ->with('', $expectedData)
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     * @expectedException \Common\Exception\BadRequestException
     */
    public function testMakeRestCallPostWithFail()
    {
        $service = 'Sample';
        $method = 'POST';
        $data = array('foo' => 'bar');
        $response = false;
        $expectedData = array(
            'data' => '{"foo":"bar"}'
        );

        $this->mockService = $this->getMock('\stdClass', array('post'));
        $this->mockService->expects($this->once())
            ->method('post')
            ->with('', $expectedData)
            ->will($this->returnValue($response));

        $this->sut->makeRestCall($service, $method, $data);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallDelete()
    {
        $service = 'Sample';
        $method = 'DELETE';
        $data = array('id' => 1);
        $response = array('id' => 1);

        $this->mockService = $this->getMock('\stdClass', array('delete'));
        $this->mockService->expects($this->once())
            ->method('delete')
            ->with('', $data)
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testMakeRestCallDeleteWithFail()
    {
        $service = 'Sample';
        $method = 'DELETE';
        $data = array('id' => 1);
        $response = false;

        $this->mockService = $this->getMock('\stdClass', array('delete'));
        $this->mockService->expects($this->once())
            ->method('delete')
            ->with('', $data)
            ->will($this->returnValue($response));

        $this->sut->makeRestCall($service, $method, $data);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     */
    public function testMakeRestCallPut()
    {
        $service = 'Sample';
        $method = 'PUT';
        $data = array('id' => 1, 'foo' => 'bar');
        $response = 200;
        $expectedData = array(
            'data' => '{"foo":"bar"}'
        );

        $this->mockService = $this->getMock('\stdClass', array('put'));
        $this->mockService->expects($this->once())
            ->method('put')
            ->with('/1', $expectedData)
            ->will($this->returnValue($response));

        $output = $this->sut->makeRestCall($service, $method, $data);

        $this->assertEquals($response, $output);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     * @expectedException \Common\Exception\BadRequestException
     */
    public function testMakeRestCallPutWith400()
    {
        $service = 'Sample';
        $method = 'PUT';
        $data = array('id' => 1, 'foo' => 'bar');
        $response = 400;
        $expectedData = array(
            'data' => '{"foo":"bar"}'
        );

        $this->mockService = $this->getMock('\stdClass', array('put'));
        $this->mockService->expects($this->once())
            ->method('put')
            ->with('/1', $expectedData)
            ->will($this->returnValue($response));

        $this->sut->makeRestCall($service, $method, $data);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     * @expectedException \Common\Exception\ResourceNotFoundException
     */
    public function testMakeRestCallPutWith404()
    {
        $service = 'Sample';
        $method = 'PUT';
        $data = array('id' => 1, 'foo' => 'bar');
        $response = 404;
        $expectedData = array(
            'data' => '{"foo":"bar"}'
        );

        $this->mockService = $this->getMock('\stdClass', array('put'));
        $this->mockService->expects($this->once())
            ->method('put')
            ->with('/1', $expectedData)
            ->will($this->returnValue($response));

        $this->sut->makeRestCall($service, $method, $data);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     * @expectedException \Common\Exception\ResourceConflictException
     */
    public function testMakeRestCallPutWith409()
    {
        $service = 'Sample';
        $method = 'PUT';
        $data = array('id' => 1, 'foo' => 'bar');
        $response = 409;
        $expectedData = array(
            'data' => '{"foo":"bar"}'
        );

        $this->mockService = $this->getMock('\stdClass', array('put'));
        $this->mockService->expects($this->once())
            ->method('put')
            ->with('/1', $expectedData)
            ->will($this->returnValue($response));

        $this->sut->makeRestCall($service, $method, $data);
    }

    /**
     * @group helper_service
     * @group rest_helper_service
     * @expectedException \Common\Exception\BadRequestException
     */
    public function testMakeRestCallPutWithUnknownMethod()
    {
        $service = 'Sample';
        $method = 'BLAH';
        $data = array('id' => 1);

        $this->sut->makeRestCall($service, $method, $data);
    }
}
