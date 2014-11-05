<?php

namespace CommonTest\Service\Data;

use Common\Service\Data\Task;
use Mockery as m;
use PHPUnit_Framework_TestCase;

/**
 * Class Task Test
 * @package CommonTest\Service\Data
 */
class TaskTest extends PHPUnit_Framework_TestCase
{
    public function testGetServiceName()
    {
        $sut = new Task();
        $this->assertEquals('Task', $sut->getServiceName());
    }
}
