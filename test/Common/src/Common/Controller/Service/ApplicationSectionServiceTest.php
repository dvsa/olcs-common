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
    protected $sut;

    protected function setUp()
    {
        $this->sut = new ApplicationSectionService();

        parent::setUp();
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

        $this->mockSectionService('Licence', $licenceService);

        $service = $this->sut->getLicenceSectionService();

        $this->assertSame($licenceService, $service);

        $this->assertEquals(7, $licenceService->getIdentifier());
    }
}
