<?php
namespace CommonTest\View\Helper;

/**
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PluginManagerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    public function testTrait()
    {
        $trait = $this->getMockForTrait('Common\View\Helper\PluginManagerAwareTrait');

        $viewHelperManager = new \Zend\View\HelperPluginManager;

        $this->assertSame($viewHelperManager, $trait->setViewHelperManager($viewHelperManager)->getViewHelperManager());
    }
}
