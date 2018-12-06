<?php

/**
 * Form Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService;

use PHPUnit_Framework_TestCase;
use CommonTest\Bootstrap;
use Common\FormService\FormServiceManagerFactory;

/**
 * Form Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new FormServiceManagerFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        // Params
        $config = [
            'form_service_manager' => [
                'invokables' => [
                    'foo' => '\stdClass'
                ]
            ]
        ];

        // Mocks
        $this->sm->setService('Config', $config);

        $brm = $this->sut->createService($this->sm);

        $this->assertInstanceOf('\Common\FormService\FormServiceManager', $brm);
        $this->assertSame($this->sm, $brm->getServiceLocator());
        $this->assertTrue($brm->has('foo'));
        $this->assertFalse($brm->has('bar'));
    }
}
