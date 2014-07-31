<?php

/**
 * Test the Service Api Resolver
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Api;

/**
 * Test the Service Api Resolver
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ServiceApiResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServiceWithApiKeysSiblings()
    {
        $config = array(
            'service_api_mapping' => array(
                'endpoints' => array(
                    'foo' => 'http://bar',
                    'backend' => 'http://backend'
                ),
                'apis' => array(
                    'foo' => array(
                        'bar' => 'baz'
                    )
                )
            )
        );
        $serviceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', ['get']);
        $serviceLocator->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));

        $resolver = new \Common\Service\Api\ServiceApiResolver();
        $instance = $resolver->createService($serviceLocator);
        $this->assertEquals('http://bar:80/baz', $instance->getClient('bar')->url());
    }

    public function testCreateServiceWithoutApiKeysSiblings()
    {
        $config = array(
            'service_api_mapping' => array(
                'endpoints' => array(
                    'foo' => 'http://bar',
                    'backend' => 'http://backend'
                ),
            )
        );
        $serviceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', ['get']);
        $serviceLocator->expects($this->once())
            ->method('get')
            ->will($this->returnValue($config));

        $resolver = new \Common\Service\Api\ServiceApiResolver();
        $instance = $resolver->createService($serviceLocator);
        $this->assertEquals('http://backend:80/bar', $instance->getClient('bar')->url());
    }
}
