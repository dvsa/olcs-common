<?php

/**
 * Grant Condition Undertaking Process Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Processing;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Processing\GrantConditionUndertakingProcessingService;

/**
 * Grant Condition Undertaking Process Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantConditionUndertakingProcessingServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new GrantConditionUndertakingProcessingService();
        $this->sm = Bootstrap::getServiceManager();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testGrantWithoutData()
    {
        // Params
        $id = 123;
        $licenceId = 321;
        $stubbedGrantData = [

        ];

        // Mocks
        $mockCuEntity = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $mockCuEntity);

        // Expectations
        $mockCuEntity->shouldReceive('getGrantData')
            ->with($id)
            ->andReturn($stubbedGrantData);

        $this->assertNull($this->sut->grant($id, $licenceId));
    }

    public function testGrantWithCreate()
    {
        // Params
        $id = 123;
        $licenceId = 321;
        $stubbedCuRecord = [
            'id' => 765,
            'application' => 543,
            'action' => 'A',
            'version' => 1,
            'foo' => 'bar',
            'cake' => 'bar'
        ];
        $stubbedGrantData = [$stubbedCuRecord];
        $stubbedUser = [
            'id' => 789
        ];
        $expectedSaveData = [
            'licence' => $licenceId,
            'isDraft' => 'N',
            'approvalUser' => 789,
            'foo' => 'bar',
            'cake' => 'bar'
        ];

        // Mocks
        $mockCuEntity = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $mockCuEntity);
        $mockUser = m::mock();
        $this->sm->setService('Entity\User', $mockUser);
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);

        // Expectations
        $mockCuEntity->shouldReceive('getGrantData')
            ->with($id)
            ->andReturn($stubbedGrantData)
            ->shouldReceive('save')
            ->with($expectedSaveData);

        $mockUser->shouldReceive('getCurrentUser')
            ->andReturn($stubbedUser);

        $mockDataHelper->shouldReceive('replaceIds')
            ->with($stubbedCuRecord)
            ->andReturn($stubbedCuRecord);

        $this->assertNull($this->sut->grant($id, $licenceId));
    }

    public function testGrantWithUpdate()
    {
        // Params
        $id = 123;
        $licenceId = 321;
        $stubbedCuRecord = [
            'id' => 765,
            'application' => 543,
            'action' => 'U',
            'version' => 1,
            'licConditionVariation' => [
                'id' => 987
            ],
            'operatingCentre' => 'oc123',
            'conditionType' => 'ct123',
            'attachedTo' => 'at123',
            'isFulfilled' => 'Y',
            'notes' => 'qwertyuiop',
            'case' => 'case123',
            'addedVia' => 'av123'
        ];
        $stubbedCurrentRecord = [
            'id' => 987,
            'foo' => 'bar',
            'licence' => 321,
            'version' => 3
        ];
        $stubbedGrantData = [$stubbedCuRecord];
        $stubbedUser = [
            'id' => 789
        ];
        $expectedSaveData = [
            'id' => 987,
            'foo' => 'bar',
            'licence' => 321,
            'version' => 3,
            'operatingCentre' => 'oc123',
            'conditionType' => 'ct123',
            'attachedTo' => 'at123',
            'isFulfilled' => 'Y',
            'notes' => 'qwertyuiop',
            'case' => 'case123',
            'addedVia' => 'av123',
            'isDraft' => 'N',
            'approvalUser' => 789
        ];

        // Mocks
        $mockCuEntity = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $mockCuEntity);
        $mockUser = m::mock();
        $this->sm->setService('Entity\User', $mockUser);
        $mockDataHelper = m::mock();
        $this->sm->setService('Helper\Data', $mockDataHelper);

        // Expectations
        $mockCuEntity->shouldReceive('getGrantData')
            ->with($id)
            ->andReturn($stubbedGrantData)
            ->shouldReceive('getCondition')
            ->with(987)
            ->andReturn($stubbedCurrentRecord)
            ->shouldReceive('forceUpdate')
            ->with(987, $expectedSaveData);

        $mockUser->shouldReceive('getCurrentUser')
            ->andReturn($stubbedUser);

        $mockDataHelper->shouldReceive('replaceIds')
            ->with($stubbedCuRecord)
            ->andReturn($stubbedCuRecord)
            ->shouldReceive('replaceIds')
            ->with($stubbedCurrentRecord)
            ->andReturn($stubbedCurrentRecord);

        $this->assertNull($this->sut->grant($id, $licenceId));
    }

    public function testGrantWithDelete()
    {
        // Params
        $id = 123;
        $licenceId = 321;
        $stubbedCuRecord = [
            'id' => 765,
            'action' => 'D',
            'licConditionVariation' => [
                'id' => 987
            ]
        ];
        $stubbedGrantData = [$stubbedCuRecord];
        $stubbedUser = [
            'id' => 789
        ];
        $expectedSaveData = [
            'approvalUser' => 789
        ];

        // Mocks
        $mockCuEntity = m::mock();
        $this->sm->setService('Entity\ConditionUndertaking', $mockCuEntity);
        $mockUser = m::mock();
        $this->sm->setService('Entity\User', $mockUser);

        // Expectations
        $mockCuEntity->shouldReceive('getGrantData')
            ->with($id)
            ->andReturn($stubbedGrantData)
            ->shouldReceive('forceUpdate')
            ->with(987, $expectedSaveData)
            ->shouldReceive('delete')
            ->with(987);

        $mockUser->shouldReceive('getCurrentUser')
            ->andReturn($stubbedUser);

        $this->assertNull($this->sut->grant($id, $licenceId));
    }
}
