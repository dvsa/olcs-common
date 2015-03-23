<?php
/**
 * Table Builder Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Table;

use Common\Service\Table\TableBuilder;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Table Builder Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class TableBuilderAwareTraitTest extends TestCase
{
    /**
     * Tests the trait's get and set methods.
     */
    public function testSetGetTableBuilder()
    {
        $tableBuilder = $this->getMock(
            '\Common\Service\Table\TableBuilder',
            [], [], '', false, true, true, false, false
        );

        /** @var \Common\Service\Table\TableBuilderAwareTrait $trait */
        $trait = $this->getMockForTrait('\Common\Service\Table\TableBuilderAwareTrait');

        $this->assertSame($tableBuilder, $trait->setTableBuilder($tableBuilder)->getTableBuilder());
    }
}
