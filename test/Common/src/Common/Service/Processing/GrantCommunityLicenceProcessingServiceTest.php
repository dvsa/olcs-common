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
}
