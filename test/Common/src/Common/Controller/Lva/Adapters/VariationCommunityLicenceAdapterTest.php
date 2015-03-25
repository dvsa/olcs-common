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

        $mockDateHelper = m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $data = [
            'status' => 'cl_sts_pending'
        ];

        $mockAddOfficeCopy = m::mock()
            ->shouldReceive('addOfficeCopy')
            ->with($data, $licenceId)
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockAddOfficeCopy);

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->andReturn(['interimStatus' => ['id' => ApplicationEntityService::INTERIM_STATUS_REQUESTED]])
            ->getMock()
        );

        $this->sut->addOfficeCopy($licenceId, $applicationId);
    }

    /**
     * Test add office copy when status is inforce
     * 
     * @group variationCommunityLicenceAdapter
     */
    public function testAddOfficeCopyWhenInForce()
    {
        $licenceId = 1;
        $applicationId = 2;

        $mockDateHelper = m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $data = [
            'status' => 'cl_sts_active',
            'specifiedDate' => '2015-01-01'
        ];

        $mockAddOfficeCopy = m::mock()
            ->shouldReceive('addOfficeCopy')
            ->with($data, $licenceId)
            ->andReturn(['id' => 25])
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockAddOfficeCopy);

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock()
        );

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->andReturn(['interimStatus' => ['id' => ApplicationEntityService::INTERIM_STATUS_INFORCE]])
            ->getMock()
        );

        $this->sm->setService(
            'Helper\CommunityLicenceDocument',
            m::mock()
            ->shouldReceive('generateBatch')
            ->with($licenceId, [25], $applicationId)
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

        $mockDateHelper = m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $data = [
            'status' => 'cl_sts_pending'
        ];

        $mockAddCommunityLicences = m::mock()
            ->shouldReceive('addCommunityLicences')
            ->with($data, $licenceId, 2)
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockAddCommunityLicences);

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->andReturn(['interimStatus' => ['id' => ApplicationEntityService::INTERIM_STATUS_REQUESTED]])
            ->getMock()
        );

        $this->sut->addCommunityLicences($licenceId, 2, $applicationId);
    }

    /**
     * Test add community licences when status is inforce
     * 
     * @group variationCommunityLicenceAdapter
     */
    public function testAddCommunityLicencesWhenInForce()
    {
        $licenceId = 1;
        $applicationId = 2;

        $mockDateHelper = m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $data = [
            'status' => 'cl_sts_active',
            'specifiedDate' => '2015-01-01'
        ];

        $mockAddCommunityLicences = m::mock()
            ->shouldReceive('addCommunityLicences')
            ->with($data, $licenceId, 2)
            ->andReturn(['id' => [25, 26]])
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockAddCommunityLicences);

        $this->sm->setService(
            'Entity\Application',
            m::mock()
            ->shouldReceive('getDataForInterim')
            ->andReturn(['interimStatus' => ['id' => ApplicationEntityService::INTERIM_STATUS_INFORCE]])
            ->getMock()
        );

        $this->sm->setService(
            'Helper\Date',
            m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock()
        );

        $this->sm->setService(
            'Helper\CommunityLicenceDocument',
            m::mock()
            ->shouldReceive('generateBatch')
            ->with($licenceId, [25, 26], $applicationId)
            ->getMock()
        );

        $this->sut->addCommunityLicences($licenceId, 2, $applicationId);
    }
}
