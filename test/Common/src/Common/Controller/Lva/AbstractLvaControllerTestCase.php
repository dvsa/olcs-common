<?php

namespace CommonTest\Controller\Lva;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\TestHelpers\Lva\Traits\LvaControllerTestTrait;

/**
 * Helper functions for testing LVA controllers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
abstract class AbstractLvaControllerTestCase extends MockeryTestCase
{
    use LvaControllerTestTrait;

    protected function getServiceManager()
    {
        return Bootstrap::getServiceManager();
    }
}
