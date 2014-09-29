<?php

/**
 * Application Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Service;

use CommonTest\Bootstrap;
use Common\Controller\Service\ApplicationSectionService;

/**
 * Application Section Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSectionServiceTest extends AbstractSectionServiceTestCase
{
    /**
     * Holds the SUT
     *
     * @var \Common\Controller\Service\ApplicationSectionService
     */
    private $sut;

    private $mockSectionServiceFactory;

    protected function setUp()
    {
        $this->mockSectionServiceFactory = $this->getMock(
            '\Common\Controller\Service\SectionServiceFactory',
            array('createSectionService')
        );

        $this->serviceManager = Bootstrap::getServiceManager();

        $this->sut = new ApplicationSectionService();
        $this->sut->setServiceLocator($this->serviceManager);
        $this->sut->setSectionServiceFactory($this->mockSectionServiceFactory);
    }

    /**
     * @group section_service
     * @group application_section_service
     */
    public function testGetLicenceSectionService()
    {
        $this->attachRestHelperMock();
        $id = 3;
        $response = array(
            'licence' => array(
                'id' => 7
            )
        );

        $this->sut->setIdentifier($id);

        $this->mockRestHelper->expects($this->once())
            ->method('makeRestCall')
            ->with('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $licenceService = new \Common\Controller\Service\LicenceSectionService();

        $this->mockSectionServiceFactory->expects($this->once())
            ->method('createSectionService')
            ->with('Licence')
            ->will($this->returnValue($licenceService));

        $service = $this->sut->getLicenceSectionService();

        $this->assertSame($licenceService, $service);

        $this->assertEquals(7, $licenceService->getIdentifier());
    }
}
