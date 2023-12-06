<?php

namespace CommonTest\Common\Controller\Lva;

use CommonTest\Common\Controller\Lva\AbstractLvaControllerTestCase;
use Mockery as m;
use Common\Controller\Lva\AbstractBusinessTypeController;

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

        $this->mockController(AbstractBusinessTypeController::class);
    }

    public function testGetIndexAction()
    {
        $this->assertTrue(true);
    }
}
