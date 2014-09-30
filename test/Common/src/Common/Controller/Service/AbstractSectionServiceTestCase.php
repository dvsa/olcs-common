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

    protected $mockedHelperServices = array();

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

        $mockHelperService = $this->getMock('\stdClass', array('getHelperService'));
        $mockHelperService->expects($this->any())
            ->method('getHelperService')
            ->will($this->returnCallback(array($this, 'getMockedHelperService')));

        $this->serviceManager->setAllowOverride(true);
        $this->serviceManager->setService('HelperService', $mockHelperService);

        $this->sut->setSectionServiceFactory($this->mockSectionService);
    }

    protected function attachRestHelperMock()
    {
        $this->mockRestHelper = $this->getMock('\Common\Service\Helper\RestHelperService', array('makeRestCall'));

        $this->mockHelperService('RestHelper', $this->mockRestHelper);
    }

    /**
     * Callback used to return mocked services
     *
     * @param string $service
     * @return \Common\Service\Helper\HelperServiceInterface
     */
    public function getMockedHelperService($service)
    {
        if (isset($this->mockedHelperServices[$service])) {
            return $this->mockedHelperServices[$service];
        }

        $this->fail('Un-mocked helper service: ' . $service);
    }

    /**
     * Set a mock helper service
     *
     * @param string $service
     * @param object $mock
     */
    protected function mockHelperService($service, $mock)
    {
        $this->mockedHelperServices[$service] = $mock;
    }

    /**
     * Callback used to return mocked services
     *
     * @param string $service
     * @return \Common\Controller\Service\SectionServiceInterface
     */
    public function getMockedSectionService($service)
    {
        if (isset($this->mockedSectionServices[$service])) {
            return $this->mockedSectionServices[$service];
        }

        $this->fail('Un-mocked section service: ' . $service);
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

    public function returnInput($input)
    {
        return $input;
    }
}
