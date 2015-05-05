<?php

/**
 * Queue Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\QueueEntityService;

/**
 * Queue Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueueEntityServiceTest extends AbstractEntityServiceTestCase
{
    public function setUp()
    {
        $this->sut = new QueueEntityService();

        parent::setUp();
    }

    public function testGetNextItemNoTypeNoItem()
    {
        $type = null;
        $expectedQuery = [
            'status' => QueueEntityService::STATUS_QUEUED,
            'limit' => 1,
            'sort' => 'createdOn',
            'order' => 'ASC',
            'processAfterDate' => [
                'NULL',
                '<=2015-01-01 10:10:10'
            ]
        ];
        $results = [
            'Results' => []
        ];

        // Mocks
        $mockDate = m::mock();
        $this->sm->setService('Helper\Date', $mockDate);

        // Expectations
        $mockDate->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2015-01-01 10:10:10');

        $this->expectOneRestCall('Queue', 'GET', $expectedQuery)
            ->will($this->returnValue($results));

        // Assertions
        $this->assertNull($this->sut->getNextItem($type));
    }

    public function testGetNextItemWithTypeNoItem()
    {
        $type = 'foo';
        $expectedQuery = [
            'status' => QueueEntityService::STATUS_QUEUED,
            'limit' => 1,
            'sort' => 'createdOn',
            'order' => 'ASC',
            'type' => 'foo',
            'processAfterDate' => [
                'NULL',
                '<=2015-01-01 10:10:10'
            ]
        ];
        $results = [
            'Results' => []
        ];

        // Mocks
        $mockDate = m::mock();
        $this->sm->setService('Helper\Date', $mockDate);

        // Expectations
        $mockDate->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2015-01-01 10:10:10');

        $this->expectOneRestCall('Queue', 'GET', $expectedQuery)
            ->will($this->returnValue($results));

        // Assertions
        $this->assertNull($this->sut->getNextItem($type));
    }

    public function testGetNextItemNoTypeWithLockedItem()
    {
        $type = null;
        $expectedQuery = [
            'status' => QueueEntityService::STATUS_QUEUED,
            'limit' => 1,
            'sort' => 'createdOn',
            'order' => 'ASC',
            'processAfterDate' => [
                'NULL',
                '<=2015-01-01 10:10:10'
            ]
        ];
        $expectedSave = [
            'id' => 111,
            'version' => 1,
            'status' => QueueEntityService::STATUS_PROCESSING
        ];
        $results = [
            'Results' => [
                [
                    'id' => 111,
                    'version' => 1
                ]
            ]
        ];

        // Mocks
        $mockDate = m::mock();
        $this->sm->setService('Helper\Date', $mockDate);

        // Expectations
        $mockDate->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2015-01-01 10:10:10');

        $this->expectedRestCallInOrder('Queue', 'GET', $expectedQuery)
            ->will($this->returnValue($results));

        $this->expectedRestCallInOrder('Queue', 'PUT', $expectedSave)
            ->will($this->throwException(new \Common\Exception\ResourceConflictException()));

        // Assertions
        $this->assertNull($this->sut->getNextItem($type));
    }

    public function testGetNextItemNoTypeWithUnlockedItem()
    {
        $type = null;
        $expectedQuery = [
            'status' => QueueEntityService::STATUS_QUEUED,
            'limit' => 1,
            'sort' => 'createdOn',
            'order' => 'ASC',
            'processAfterDate' => [
                'NULL',
                '<=2015-01-01 10:10:10'
            ]
        ];
        $expectedSave = [
            'id' => 111,
            'version' => 1,
            'status' => QueueEntityService::STATUS_PROCESSING
        ];
        $result = [
            'id' => 111,
            'version' => 1
        ];
        $results = [
            'Results' => [
                $result
            ]
        ];

        // Mocks
        $mockDate = m::mock();
        $this->sm->setService('Helper\Date', $mockDate);

        // Expectations
        $mockDate->shouldReceive('getDate')
            ->with(\DateTime::W3C)
            ->andReturn('2015-01-01 10:10:10');

        $this->expectedRestCallInOrder('Queue', 'GET', $expectedQuery)
            ->will($this->returnValue($results));

        $this->expectedRestCallInOrder('Queue', 'PUT', $expectedSave);

        // Assertions
        $this->assertEquals($result, $this->sut->getNextItem($type));
    }
}
