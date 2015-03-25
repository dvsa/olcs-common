<?php

/**
 * Variation Community Licence Adapter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationCommunityLicenceAdapter;
use Common\Service\Entity\ApplicationEntityService;

/**
 * Variation Community Licence Adapter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VariationCommunityLicenceAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);
        $this->sut = new VariationCommunityLicenceAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * Test add office copy
     * 
     * @group variationCommunityLicenceAdapter
     */
    public function testAddOfficeCopy()
    {
        $licenceId = 1;
        $applicationId = 2;
        $this->sm->setService(
            'ApplicationCommunityLicenceAdapter',
            m::mock()
            ->shouldReceive('addOfficeCopy')
            ->with($licenceId, $applicationId)
            ->once()
            ->getMock()
        );

        $this->sut->addOfficeCopy($licenceId, $applicationId);
    }

    /**
     * Test get total authority
     * 
     * @group variationCommunityLicenceAdapter
     */
    public function testGetTotalAuthority()
    {
        $id = 1;

        $mockApplicationService = m::mock()
            ->shouldReceive('getById')
            ->with($id)
            ->andReturn(['totAuthVehicles' => 5])
            ->getMock();
        $this->sm->setService('Entity\Application', $mockApplicationService);

        $this->assertEquals($this->sut->getTotalAuthority($id), 5);
    }

    /**
     * Test add commuinty licences
     * 
     * @group variationCommunityLicenceAdapter
     */
    public function testAddCommunityLicences()
    {
        $licenceId = 1;
        $applicationId = 2;
        $this->sm->setService(
            'ApplicationCommunityLicenceAdapter',
            m::mock()
            ->shouldReceive('addCommunityLicences')
            ->with($licenceId, 2, $applicationId)
            ->once()
            ->getMock()
        );
        $this->sut->addCommunityLicences($licenceId, 2, $applicationId);
    }
}
