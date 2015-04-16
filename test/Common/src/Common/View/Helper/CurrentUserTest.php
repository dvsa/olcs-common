<?php

/**
 * Test TransportManagerApplicationStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */

namespace CommonTest\View\Helper;

use \Common\View\Helper\CurrentUser;

/**
 * Test TransportManagerApplicationStatus view helper
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CurrentUserTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Setup the view helper
     */
    public function setUp()
    {
        $this->sut = new CurrentUser();
    }

    public function testGetFullName()
    {
        $this->assertEquals('Terry Barret-Edgecombe', $this->sut->getFullName());
    }
}
