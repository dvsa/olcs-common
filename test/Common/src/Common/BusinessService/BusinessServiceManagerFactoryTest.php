<?php

/**
 * Business Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService;

use PHPUnit_Framework_TestCase;
use CommonTest\Bootstrap;
use Common\BusinessService\BusinessServiceManagerFactory;

/**
 * Business Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessServiceManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new BusinessServiceManagerFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        // Params
        $config = [
            'business_service_manager' => [
                'invokables' => [
                    'foo' => '\stdClass'
                ]
            ]
        ];

        // Mocks
        $this->sm->setService('Config', $config);

        $brm = $this->sut->createService($this->sm);

        $this->assertInstanceOf('\Common\BusinessService\BusinessServiceManager', $brm);
        $this->assertSame($this->sm, $brm->getServiceLocator());
        $this->assertTrue($brm->has('foo'));
        $this->assertFalse($brm->has('bar'));
    }
}
