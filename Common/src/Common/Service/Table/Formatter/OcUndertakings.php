<?php

/**
 * OcUndertakings.php
 */
namespace Common\Service\Table\Formatter;

use Common\Service\Entity\ConditionUndertakingEntityService;

/**
 * Class OcUndertakings
 *
 * Format results for the table.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class OcUndertakings implements FormatterInterface
{
    /**
     * Get the undertakings for the operating centre and return a count.
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

        if (!is_null($data['undertakings'])) {
            foreach ($data['undertakings'] as $undertaking) {
                if ($undertaking['conditionType']['id'] === ConditionUndertakingEntityService::TYPE_UNDERTAKING) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
