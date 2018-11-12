<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * IRHP Permit Jurisdiction Table - Permit Number column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitJurisdictionPermitNumber implements FormatterInterface
{
    /**
     * Format
     *
     * Returns an editable Jurisdiction Permit Number
     *
     * @param array $data
     *
     * @return string
     */
    public static function format($data)
    {
        $quotaNumber = $data['quotaNumber'] ? Escape::html($data['quotaNumber']) : 0;
        $id = Escape::html($data['id']);

        return sprintf(
            "<input type='number' value='%s' name='trafficAreas[%s]' />",
            $quotaNumber,
            $id
        );
    }
}
