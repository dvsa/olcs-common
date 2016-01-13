<?php

/**
 * LvaAdapter Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Factories;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Factories\ApplicationLvaAdapterFactory;
use Common\Controller\Lva\Factories\LicenceLvaAdapterFactory;
use Common\Controller\Lva\Factories\VariationLvaAdapterFactory;

/**
 * LvaAdapter Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LvaAdapterFactoryTest extends MockeryTestCase
{
    protected $sm;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);
    }

    public function testCreateApplicationService()
    {
        $sut = new ApplicationLvaAdapterFactory();

        $service = $sut->createService($this->sm);

        $this->assertInstanceOf('\Common\Controller\Lva\Adapters\ApplicationLvaAdapter', $service);
    }

    public function testCreateLicenceService()
    {
        $sut = new LicenceLvaAdapterFactory();

        $service = $sut->createService($this->sm);

        $this->assertInstanceOf('\Common\Controller\Lva\Adapters\LicenceLvaAdapter', $service);
    }

    public function testCreateVariationService()
    {
        $sut = new VariationLvaAdapterFactory();

        $service = $sut->createService($this->sm);

        $this->assertInstanceOf('\Common\Controller\Lva\Adapters\VariationLvaAdapter', $service);
    }
}
