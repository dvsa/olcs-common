<?php

namespace Common\Service\Table\Formatter;

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
        $quotaNumber = $data['quotaNumber'] ? $data['quotaNumber'] : 0;
        $sectorId = $data['sector']['id'];

        return "<input type='number' value='$quotaNumber' name='sectors[$sectorId]' />";
    }
}
