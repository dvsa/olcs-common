<?php

/**
 * Schedule41EntityServiceTest.php
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\Schedule41EntityService;
use Mockery as m;

/**
 * Class Schedule41EntityServiceTest
 *
 * Schedule 41 entity service tests.
 *
 * @package CommonTest\Service\Entity]
 */
class Schedule41EntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new Schedule41EntityService();

        parent::setUp();
    }

    public function testGetByApplicationId()
    {
        $id = 1;

        $this->expectOneRestCall('s4', 'GET', array('limit' => 'all', 'application' => $id))
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getByApplication($id));
    }
}
