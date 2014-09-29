<?php

/**
 * Abstract Section Service Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;

/**
 * Abstract Section Service Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractSectionServiceTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\SectionServiceInterface
     */
    protected $sut;

    /**
     * Service manager
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Mock rest helper
     *
     * @var \Common\Service\Helper\RestHelperService
     */
    protected $mockRestHelper;

    /**
     * Mock section service
     *
     * @var \Common\Controller\Service\SectionServiceFactory
     */
    protected $mockSectionService;

    protected $mockedSectionServices = array();

    protected function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->serviceManager);

        $this->mockSectionService = $this->getMock(
            '\Common\Controller\Service\SectionServiceFactory',
            array(
                'getSectionService',
                'createSectionService'
            )
        );
        $this->mockSectionService->expects($this->any())
            ->method('getSectionService')
            ->will($this->returnCallback(array($this, 'getMockedSectionService')));
        $this->mockSectionService->expects($this->any())
            ->method('createSectionService')
            ->will($this->returnCallback(array($this, 'getMockedSectionService')));

        $this->sut->setSectionServiceFactory($this->mockSectionService);
    }

    protected function attachRestHelperMock()
    {
        $this->mockRestHelper = $this->getMock('\Common\Service\Helper\RestHelperService', array('makeRestCall'));

        $mockHelperService = $this->getMock('\stdClass', array('getHelperService'));
        $mockHelperService->expects($this->any())
            ->method('getHelperService')
            ->with('RestHelper')
            ->will($this->returnValue($this->mockRestHelper));

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('HelperService', $mockHelperService);
    }

    /**
     * Callback used to return mocked services
     *
     * @param string $service
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    public function getMockedSectionService($service)
    {
        return isset($this->mockedSectionServices[$service]) ? $this->mockedSectionServices[$service] : null;
    }

    /**
     * Set a mock section service
     *
     * @param string $service
     * @param object $mock
     */
    protected function mockSectionService($service, $mock)
    {
        $this->mockedSectionServices[$service] = $mock;
    }
}
