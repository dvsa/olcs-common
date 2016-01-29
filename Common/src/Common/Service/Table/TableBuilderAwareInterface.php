<?php
/**
 * Table Builder Aware Interface
 *
 * @package Common\Service\Table
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Common\Service\Table;

use Common\Service\Table\TableBuilder as CommonTableBuilder;

/**
 * Table Builder Aware Interface
 *
 * @package Common\Service\Table
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
interface TableBuilderAwareInterface
{
    /**
     * Gets an instance of \Common\Service\Table\TableBuilder.
     *
     * @return CommonTableBuilder
     */
    public function getTableBuilder();

    /**
     * Sets an instance of \Common\Service\Table\TableBuilder.
     *
     * @param CommonTableBuilder $tableBuilder
     * @return self Fluent interface required
     */
    public function setTableBuilder(CommonTableBuilder $tableBuilder);
}
