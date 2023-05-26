<?php

/**
 * Task checkbox formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Table\TableBuilder;

/**
 * Task checkbox formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaskCheckbox implements FormatterPluginManagerInterface
{
    private TableBuilder $tableBuilder;

    public function __construct(TableBuilder $tableBuilder)
    {
        $this->tableBuilder = $tableBuilder;
    }
    /**
     * Format a task checkbox
     *
     * @param      array $data
     * @param      array $column
     * @return     string
     * @inheritdoc
     */
    public function format($data, $column = [])
    {
        if (isset($data['isClosed']) && $data['isClosed'] === 'Y') {
            return '';
        }

        return $this->tableBuilder->replaceContent('{{[elements/checkbox]}}', $data);
    }
}
