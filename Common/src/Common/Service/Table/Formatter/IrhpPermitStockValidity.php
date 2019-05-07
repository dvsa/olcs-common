<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceManager;
use Common\Service\Table\Formatter\Date as DateFormatter;
use Common\Util\Escape;

/**
 * IRHP Permit Stock table - Validity Period column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitStockValidity implements FormatterInterface
{
    /**
     * Format
     *
     * Returns a formatted date of the Validity Period
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (is_null($data['validFrom']) || is_null($data['validTo'])) {
            return 'N/A';
        }

        $validFrom = DateFormatter::format(['validFrom' => $data['validFrom']], $column, $sm);
        $validTo = DateFormatter::format(['validFrom' => $data['validTo']], $column, $sm);

        return $validFrom .  " to " . $validTo;
    }
}
