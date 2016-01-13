<?php

/**
 * CommunityLicWithdrawal Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\CommunityLicWithdrawalEntityService;
use Mockery as m;

/**
 * CommunityLicWithdrawal Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicWithdrawalServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CommunityLicWithdrawalEntityService();

        parent::setUp();
    }

    /**
     * @group communityLicWithdrawal
     */
    public function testDeleteWithdrawalAndReasons()
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
            'communityLicWithdrawal' => 'IN [1,2]'
        ];

        $this->expectedRestCallInOrder('CommunityLicWithdrawal', 'GET', $query)
            ->will($this->returnValue($data));

        $communityLicWithdrawalReasonService = m::mock()
            ->shouldReceive('deleteList')
            ->with($reasonsQuery)
            ->getMock();
        $this->sm->setService('Entity\CommunityLicWithdrawalReason', $communityLicWithdrawalReasonService);

        $this->expectedRestCallInOrder('CommunityLicWithdrawal', 'DELETE', $query);

        $this->sut->deleteWithdrawalsAndReasons($ids);
    }
}
