<?php

/**
 * Business Rule Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessRule;

use PHPUnit_Framework_TestCase;
use CommonTest\Bootstrap;
use Common\BusinessRule\BusinessRuleManagerFactory;

/**
 * Business Rule Manager Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessRuleManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new BusinessRuleManagerFactory();

        $this->sm = Bootstrap::getServiceManager();
    }

    public function testCreateService()
    {
        // Params
        $config = [
            'business_rule_manager' => [
                'invokables' => [
                    'foo' => '\stdClass'
                ]
            ]
        ];

        // Mocks
        $this->sm->setService('Config', $config);

        $brm = $this->sut->createService($this->sm);

        $this->assertInstanceOf('\Common\BusinessRule\BusinessRuleManager', $brm);
        $this->assertSame($this->sm, $brm->getServiceLocator());
        $this->assertTrue($brm->has('foo'));
        $this->assertFalse($brm->has('bar'));
    }
}
