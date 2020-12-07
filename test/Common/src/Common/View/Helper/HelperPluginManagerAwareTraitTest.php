<?php

namespace CommonTest\View\Helper;

/**
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class HelperPluginManagerAwareTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testTrait()
    {
        $trait = $this->getMockForTrait('Common\View\Helper\PluginManagerAwareTrait');

        $viewHelperManager = new \Laminas\View\HelperPluginManager;

        $this->assertSame($viewHelperManager, $trait->setViewHelperManager($viewHelperManager)->getViewHelperManager());
    }
}
