<?php

/**
 * CommunityLicSuspension Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\CommunityLicSuspensionEntityService;
use Mockery as m;

/**
 * CommunityLicSuspension Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicSuspensionServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CommunityLicSuspensionEntityService();

        parent::setUp();
    }

    /**
     * @group communityLicSuspension
     */
    public function testDeleteSuspensionsAndReasons()
    {
        $ids = [1];
        $query = [
            'communityLic' => 'IN [1]'
        ];
        $data = [
            'Results' => [
                ['id' => 1],
                ['id' => 2]
            ],
            'Count' => 2
        ];
        $reasonsQuery = [
            'communityLicSuspension' => 'IN [1,2]'
        ];

        $this->expectedRestCallInOrder('CommunityLicSuspension', 'GET', $query)
            ->will($this->returnValue($data));

        $communityLiSuspensionReasonService = m::mock()
            ->shouldReceive('deleteList')
            ->with($reasonsQuery)
            ->getMock();
        $this->sm->setService('Entity\CommunityLicSuspensionReason', $communityLiSuspensionReasonService);

        $this->expectedRestCallInOrder('CommunityLicSuspension', 'DELETE', $query);

        $this->sut->deleteSuspensionsAndReasons($ids);
    }
}
