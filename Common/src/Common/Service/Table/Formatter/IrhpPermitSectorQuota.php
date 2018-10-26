<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * IRHP Permit Sector table - Quota Number column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitSectorQuota implements FormatterInterface
{
    /**
     * Format
     *
     * Returns an editable Sector Quota Number
     *
     * @param array $data
     *
     * @return string
     */
    public static function format($data)
    {
        $quotaNumber = $data['quotaNumber'] ? Escape::html($data['quotaNumber']) : 0;
        $sectorId = Escape::html($data['sector']['id']);

        return sprintf(
            "<input type='number' value='%s' name='sectors[%s]' />",
            $quotaNumber,
            $sectorId
        );
    }
}
