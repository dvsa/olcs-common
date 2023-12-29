<?php

/**
 * Form Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService;

use Common\FormService\FormServiceManager;
use Common\FormService\FormServiceManagerFactory;
use Mockery as m;

/**
 * Form Service Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormServiceManagerFactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $sut;

    protected $sm;

    public function setUp(): void
    {
        $this->sut = new FormServiceManagerFactory();

        $this->sm = m::mock('\Laminas\ServiceManager\ServiceManager')
            ->makePartial()
            ->setAllowOverride(true);

        // inject a real string helper
        $this->sm->setService('Helper\String', new \Common\Service\Helper\StringHelperService());

    }

    public function testInvoke()
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

        $brm = $this->sut->__invoke($this->sm, FormServiceManager::class);

        $this->assertInstanceOf(FormServiceManager::class, $brm);
        $this->assertSame($this->sm, $brm->getServiceLocator());
        $this->assertTrue($brm->has('foo'));
        $this->assertFalse($brm->has('bar'));
    }
}
