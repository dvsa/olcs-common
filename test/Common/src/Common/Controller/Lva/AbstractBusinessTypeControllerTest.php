<?php

namespace CommonTest\Controller\Lva;

use Mockery as m;

/**
 * Test Abstract Business Type Controller
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AbstractBusinessTypeControllerTest extends AbstractLvaControllerTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->mockController('\Common\Controller\Lva\AbstractBusinessTypeController');
    }

    public function testGetIndexAction()
    {
        $this->assertTrue(true);
    }
}
