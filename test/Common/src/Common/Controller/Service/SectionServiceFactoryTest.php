<?php

/**
 * Section Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Section;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Common\Controller\Service\SectionServiceFactory;

/**
 * Section Service Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SectionServiceFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\SectionServiceFactoryTest
     */
    protected $sut;

    protected $serviceManager;

    /**
     * Setup the sut
     */
    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();

        $this->sut = new SectionServiceFactory();
    }

    /**
     * @group section_service
     * @group section_service_factory
     */
    public function testCreateService()
    {
        $factory = $this->sut->createService($this->serviceManager);

        $this->assertSame($factory, $this->sut);
    }

    /**
     * @group section_service
     * @group section_service_factory
     */
    public function testGetSectionService()
    {
        $this->sut->createService($this->serviceManager);

        $helper = $this->sut->getSectionService('Licence');

        $this->assertInstanceOf('\Common\Controller\Service\LicenceSectionService', $helper);

        $helper2 = $this->sut->getSectionService('Licence');

        $this->assertSame($helper, $helper2);
    }
}
