<?php

/**
 * Grant Community Licence Process Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Processing\GrantCommunityLicenceProcessingService;
use Common\Service\Entity\CommunityLicEntityService;

/**
 * Grant Community Licence Process Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantCommunityLicenceProcessingServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new GrantCommunityLicenceProcessingService();
        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGrant()
    {
        // Params
        $licenceId = 321;
        $stubbedPendingLicences = [
            [
                'id' => 12,
                'foo' => 'bar'
            ],
            [
                'id' => 34,
                'foo' => 'cake'
            ]
        ];
        $expectedData = [
            [
                'id' => 12,
                'foo' => 'bar',
                'status' => CommunityLicEntityService::STATUS_ACTIVE,
                'specifiedDate' => '2012-01-01'
            ],
            [
                'id' => 34,
                'foo' => 'cake',
                'status' => CommunityLicEntityService::STATUS_ACTIVE,
                'specifiedDate' => '2012-01-01'
            ]
        ];

        // Mocks
        $mockCommunityLic = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLic);
        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);
        $mockDocHelper = m::mock();
        $this->sm->setService('Helper\CommunityLicenceDocument', $mockDocHelper);

        // Expectations
        $mockCommunityLic->shouldReceive('getPendingForLicence')
            ->with($licenceId)
            ->andReturn($stubbedPendingLicences)
            ->shouldReceive('multiUpdate')
            ->with($expectedData);

        $mockDateHelper->shouldReceive('getDate')
            ->andReturn('2012-01-01');

        $mockDocHelper->shouldReceive('generateBatch')
            ->with(321, [12, 34]);

        $this->sut->grant($licenceId);
    }

    /**
     * Test Community Licences granted when licence can have them
     */
    public function testVoidOrGrantGrant()
    {
        $licenceId = 634;

        $mockEntityLicence = m::mock();
        $this->sm->setService('Entity\Licence', $mockEntityLicence);
        $mockEntityLicence->shouldReceive('getOverview')
            ->with($licenceId)
            ->andReturn(['id' => 121, 'foo' => 'bar']);

        $this->sut = m::mock('Common\Service\Processing\GrantCommunityLicenceProcessingService')
            ->makePartial();
        $this->sut->setServiceLocator($this->sm);

        $this->sut->shouldReceive('canHaveCommunityLicences')
            ->with(['id' => 121, 'foo' => 'bar'])
            ->andReturn(true);
        $this->sut->shouldReceive('grant')
            ->with($licenceId);
        $this->sut->shouldNotReceive('voidActivePending');

        $this->sut->voidOrGrant($licenceId);
    }

    /**
     * Test Community Licences voided when licence can NOT have them
     */
    public function testVoidOrGrantVoid()
    {
        $licenceId = 634;

        $mockEntityLicence = m::mock();
        $this->sm->setService('Entity\Licence', $mockEntityLicence);
        $mockEntityLicence->shouldReceive('getOverview')
            ->with($licenceId)
            ->andReturn(['id' => 121, 'foo' => 'bar']);

        $this->sut = m::mock('Common\Service\Processing\GrantCommunityLicenceProcessingService')
            ->makePartial();
        $this->sut->setServiceLocator($this->sm);

        $this->sut->shouldReceive('canHaveCommunityLicences')
            ->with(['id' => 121, 'foo' => 'bar'])
            ->andReturn(false);
        $this->sut->shouldNotReceive('grant');
        $this->sut->shouldReceive('voidActivePending')
            ->with($licenceId);

        $this->sut->voidOrGrant($licenceId);
    }

    /**
     * Data provider for testCanHaveCommunityLicences
     * 
     * @return array
     */
    public function canHaveCommunityLicencesData()
    {
        return [
            ['ltyp_r', 'lcat_psv', true],
            ['ltyp_r', 'lcat_gv', false],
            ['ltyp_sr', 'lcat_psv', false],
            ['ltyp_sr', 'lcat_gv', false],
            ['ltyp_si', 'lcat_psv', true],
            ['ltyp_si', 'lcat_gv', true],
            ['ltyp_sn', 'lcat_psv', false],
            ['ltyp_sn', 'lcat_gv', false],
        ];
    }

    /**
     * Test can have community licences with all combinations
     *
     * @dataProvider canHaveCommunityLicencesData
     */
    public function testCanHaveCommunityLicences($licenceType, $goodsOrPsv, $allowed)
    {
        $licence = [
            'licenceType' => ['id' => $licenceType],
            'goodsOrPsv'  => ['id' => $goodsOrPsv],
        ];

        $this->assertEquals($allowed, $this->sut->canHaveCommunityLicences($licence));
    }

    /**
     * Test voiding all active/pending comunity licences
     */
    public function testVoidActivePending()
    {
        // Params
        $licenceId = 321;
        $communityLicences = [
            [
                'id' => 12,
                'foo' => 'bar',
            ],
            [
                'id' => 34,
                'foo' => 'cake',
            ]
        ];
        $expectedData = [
            [
                'id' => 12,
                'foo' => 'bar',
                'status' => CommunityLicEntityService::STATUS_RETURNDED,
                'expiredDate' => '2015-02-12'
            ],
            [
                'id' => 34,
                'foo' => 'cake',
                'status' => CommunityLicEntityService::STATUS_RETURNDED,
                'expiredDate' => '2015-02-12'
            ]
        ];

        // Mocks
        $mockCommunityLic = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLic);
        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        // Expectations
        $mockCommunityLic->shouldReceive('getActivePendingLicences')
            ->with($licenceId)
            ->andReturn($communityLicences)
            ->shouldReceive('multiUpdate')
            ->with($expectedData);
        $mockDateHelper->shouldReceive('getDate')
            ->andReturn('2015-02-12');

        $this->sut->voidActivePending($licenceId);
    }
}
