<?php
/**
 * Table Builder Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Table;

use Common\Service\Table\TableBuilder;

/**
 * Table Builder Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class TableBuilderAwareTraitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests the trait's get and set methods.
     */
    public function testSetGetTableBuilder()
    {
        $tableBuilder = $this->createMock(
            '\Common\Service\Table\TableBuilder',
            [],
            [],
            '',
            false,
            true,
            true,
            false,
            false
        );

        /** @var \Common\Service\Table\TableBuilderAwareTrait $trait */
        $trait = $this->getMockForTrait('\Common\Service\Table\TableBuilderAwareTrait');

        $this->assertSame($tableBuilder, $trait->setTableBuilder($tableBuilder)->getTableBuilder());
    }
}
