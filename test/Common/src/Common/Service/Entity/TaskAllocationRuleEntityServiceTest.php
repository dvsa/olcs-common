<?php

/**
 * Task Allocation Rule Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TaskAllocationRuleEntityService;

/**
 * Task Allocation Rule Entity Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskAllocationRuleEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TaskAllocationRuleEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testFindByQuery()
    {
        $this->expectOneRestCall('TaskAllocationRule', 'GET', ['foo' => 'bar'])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->findByQuery(['foo' => 'bar']));
    }
}
