<?php

/**
 * Abstract Section Service Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service;

use PHPUnit_Framework_TestCase;

/**
 * Abstract Section Service Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractSectionServiceTestCase extends PHPUnit_Framework_TestCase
{
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
}
