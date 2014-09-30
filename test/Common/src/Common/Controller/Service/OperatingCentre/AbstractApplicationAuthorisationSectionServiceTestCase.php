<?php

/**
 * Abstract Application Authorisation Section Service Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service\OperatingCentre;

/**
 * Abstract Application Authorisation Section Service Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractApplicationAuthorisationSectionServiceTestCase
    extends AbstractAuthorisationSectionServiceTestCase
{

    protected function getMockLicenceSectionService()
    {
        $mockLicenceService = parent::getMockLicenceSectionService();

        $mockApplicationService = $this->getMock(
            '\Common\Controller\Service\ApplicationSectionService',
            array('getLicenceSectionService')
        );

        $mockApplicationService->expects($this->any())
            ->method('getLicenceSectionService')
            ->will($this->returnValue($mockLicenceService));

        $this->mockSectionService('Application', $mockApplicationService);

        return $mockLicenceService;
    }
}
