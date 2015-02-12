<?php

namespace CommonTest\View\Helper;

/**
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class HelperPluginManagerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testTrait()
    {
        $trait = $this->getMockForTrait('Common\View\Helper\PluginManagerAwareTrait');

        $viewHelperManager = new \Zend\View\HelperPluginManager;

        $this->assertSame($viewHelperManager, $trait->setViewHelperManager($viewHelperManager)->getViewHelperManager());
    }
}
