<?php

/**
 * Licence Community Licence Adapter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceCommunityLicenceAdapter;

/**
 * Licence Community Licence Adapter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceCommunityLicenceAdapterTest extends MockeryTestCase
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
        $this->sut = new LicenceCommunityLicenceAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * Test add office copy
     *
     * @group licenceCommunityLicenceAdapter
     */
    public function testAddOfficeCopy()
    {
        $licenceId = 1;

        $mockDateHelper = m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $data = [
            'specifiedDate' => '2015-01-01',
            'status' => 'cl_sts_active'
        ];

        $mockAddOfficeCopy = m::mock()
            ->shouldReceive('addOfficeCopy')
            ->with($data, $licenceId)
            ->getMock();
        $this->sm->setService('Entity\CommunityLic', $mockAddOfficeCopy);

        $this->sut->addOfficeCopy($licenceId);
    }

    /**
     * Test get total authority
     *
     * @group licenceCommunityLicenceAdapter
     */
    public function testGetTotalAuthority()
    {
        $id = 1;

        $mockLicenceService = m::mock()
            ->shouldReceive('getById')
            ->with($id)
            ->andReturn(['totAuthVehicles' => 5])
            ->getMock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $this->assertEquals($this->sut->getTotalAuthority($id), 5);
    }

    /**
     * Test add commuinty licences
     *
     * @group licenceCommunityLicenceAdapter
     */
    public function testAddCommunityLicences()
    {
        $licenceId = 1;

        $mockDateHelper = m::mock()
            ->shouldReceive('getDate')
            ->andReturn('2015-01-01')
            ->getMock();

        $this->sm->setService('Helper\Date', $mockDateHelper);

        $data = [
            'specifiedDate' => '2015-01-01',
            'status' => 'cl_sts_active'
        ];

        $mockAddCommunityLicences = m::mock()
            ->shouldReceive('addCommunityLicences')
            ->with($data, $licenceId, 2)
            ->andReturn(
                [
                    'id' => [1, 2, 3]
                ]
            )
            ->getMock();

        $this->sm->setService('Entity\CommunityLic', $mockAddCommunityLicences);

        $this->sm->setService(
            'Helper\CommunityLicenceDocument',
            m::mock()
            ->shouldReceive('generateBatch')
            ->with([1, 2, 3])
            ->andReturn('foo')
            ->getMock()
        );

        $this->assertEquals(
            'foo',
            $this->sut->addCommunityLicences($licenceId, 2)
        );
    }
}
