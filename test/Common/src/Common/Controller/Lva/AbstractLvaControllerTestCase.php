<?php

namespace CommonTest\Common\Controller\Lva;

use CommonTest\Bootstrap;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Helper functions for testing LVA controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractLvaControllerTestCase extends MockeryTestCase
{
    protected function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }
}
