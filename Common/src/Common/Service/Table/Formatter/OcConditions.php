<?php

/**
 * OcConditions.php
 */
namespace Common\Service\Table\Formatter;

use Common\RefData;

/**
 * Class OcConditions
 *
 * Format results for the table.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class OcConditions implements FormatterInterface
{
    /**
     * Get the conditions for the operating centre and return a count.
     *
     * @param array $data The row data.
     * @param array $column The column data.
     * @param null $sm The service manager.
     *
     * @return mixed
     */
    public static function format($data, $column = array(), $sm = null)
    {
        unset($column, $sm);

        $count = 0;

        if (!is_null($data['conditions'])) {
            foreach ($data['conditions'] as $condition) {
                if (!is_null($condition['licence']) &&
                    $condition['conditionType']['id'] === RefData::TYPE_CONDITION
                ) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
