<?php

/**
 *
 */

namespace CommonTest\Controller;

class AbstractActionControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testTest()
    {
        $this->getMockForAbstractClass('\Common\Controller\AbstractActionController');

        $this->assertTrue(true);
    }
}
