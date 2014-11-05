<?php

/**
 * Test rest call trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Util;

use PHPUnit_Framework_TestCase;

/**
 * Test rest call trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RestCallTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * Subject under test
     *
     * @var \Common\Util\RestCallTrait
     */
    private $sut;

    private $mockHelperService;

    public function setUp()
    {
        $this->mockHelperService = $this->getMock(
            '\stdClass',
            array('sendGet', 'sendPost', 'makeRestCall', 'getRestClient')
        );

        $mockServiceLocator = \Mockery::mock('\Zend\ServiceManager\ServiceManager');
        $mockServiceLocator->shouldReceive('get')->andReturn($this->mockHelperService);

        $this->sut = $this->getMockForTrait(
            '\Common\Util\RestCallTrait',
            array(),
            '',
            true,
            true,
            true,
            array(
                'getServiceLocator'
            )
        );

        $this->sut->expects($this->any())
            ->method('getServiceLocator')
            ->will($this->returnValue($mockServiceLocator));
    }

    /**
     * @group util
     * @group rest_call_trait_util
     */
    public function testSendGet()
    {
        $service = 'Service';
        $data = array(
            'foo' => 'bar'
        );
        $appendParamsToRoute = false;

        $this->mockHelperService->expects($this->once())
            ->method('sendGet')
            ->with($service, $data, $appendParamsToRoute);

        $this->sut->sendGet($service, $data, $appendParamsToRoute);
    }

    /**
     * @group util
     * @group rest_call_trait_util
     */
    public function testSendPost()
    {
        $service = 'Service';
        $data = array(
            'foo' => 'bar'
        );

        $this->mockHelperService->expects($this->once())
            ->method('sendPost')
            ->with($service, $data);

        $this->sut->sendPost($service, $data);
    }

    /**
     * @group util
     * @group rest_call_trait_util
     */
    public function testMakeRestCall()
    {
        $service = 'Service';
        $method = 'GET';
        $data = array(
            'foo' => 'bar'
        );

        $this->mockHelperService->expects($this->once())
            ->method('makeRestCall')
            ->with($service, $method, $data);

        $this->sut->makeRestCall($service, $method, $data);
    }

    /**
     * @group util
     * @group rest_call_trait_util
     */
    public function testGetRestClient()
    {
        $service = 'Service';

        $this->mockHelperService->expects($this->once())
            ->method('getRestClient')
            ->with($service);

        $this->sut->getRestClient($service);
    }
}
