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
        // Not tested as the helper is a temporary implemntation
        $this->markTestIncomplete();

        $this->assertEquals('Terry Barret-Edgecombe', $this->sut->getFullName());
    }

    public function testGetOrganisationName()
    {
        // Not tested as the helper is a temporary implemntation
        $this->markTestIncomplete();

        $this->assertEquals('John Smith Haulage Ltd', $this->sut->getOrganisationName());
    }
}
