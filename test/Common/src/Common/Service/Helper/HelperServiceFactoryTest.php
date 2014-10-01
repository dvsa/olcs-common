<?php

/**
 * Helper Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Helper;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Common\Service\Helper\HelperServiceFactory;

/**
 * Helper Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class HelperServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Service\Helper\HelperServiceFactoryTest
     */
    private $sut;

    private $serviceManager;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->sut = new HelperServiceFactory();
    }

    /**
     * @group helper_service
     * @group helper_service_factory
     */
    public function testCreateService()
    {
        $factory = $this->sut->createService($this->serviceManager);

        $this->assertSame($factory, $this->sut);
    }

    /**
     * @group helper_service
     * @group helper_service_factory
     */
    public function testGetHelperService()
    {
        $this->sut->createService($this->serviceManager);

        $helper = $this->sut->getHelperService('StringHelper');

        $this->assertInstanceOf('\Common\Service\Helper\StringHelperService', $helper);

        $helper2 = $this->sut->getHelperService('StringHelper');

        $this->assertSame($helper, $helper2);
    }
}
