<?php

namespace CommonTest\Service;

use Common\Service\Task;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class TaskTest
 * @package CommonTest\Service
 */
class TaskTest extends TestCase
{
    public function testCreateNrTask()
    {
        $case = 29;
        $licence = 7;

        $sut = new Task();
        $result = $sut->createNrTask($case, $licence);

        $this->assertInternalType('array', $result);
        $this->assertSame($licence, $result['licence']);
        $this->assertSame($case, $result['case']);

        $this->assertEquals(Task::NR_TEAM_DEFAULT, $result['assignedToTeam']);
        $this->assertEquals(Task::NR_USER_DEFAULT, $result['assignedToUser']);
        $this->assertEquals(Task::NR_CATEGORY_DEFAULT, $result['category']);
        $this->assertEquals(Task::NR_SUB_CATEGORY_DEFAULT, $result['subCategory']);
        $this->assertEquals(Task::NR_URGENT_DEFAULT, $result['urgent']);
        $this->assertEquals(Task::NR_DEFAULT_DESCRIPTION, $result['description']);

        return $result;
    }
}
