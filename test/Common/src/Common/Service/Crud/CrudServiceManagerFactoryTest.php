<?php

/**
 * Crud Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Crud;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Crud\CrudServiceManagerFactory;
use CommonTest\Bootstrap;

/**
 * Crud Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CrudServiceManagerFactoryTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new CrudServiceManagerFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        $config = [
            'crud_service_manager' => [
                'invokables' => [
                    'foo' => 'bar'
                ]
            ]
        ];
        $this->sm->setService('Config', $config);

        $pluginManager = $this->sut->createService($this->sm);

        $this->assertInstanceOf('\Common\Service\Crud\CrudServiceManager', $pluginManager);

        $this->assertTrue($pluginManager->has('foo'));
    }
}
