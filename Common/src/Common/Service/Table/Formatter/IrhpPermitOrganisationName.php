<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * IRHP Permit Organisation Name formatter
 */
class IrhpPermitOrganisationName implements FormatterInterface
{
    /**
     * Format
     *
     * Returns the IRHP Permit Organisation Name
     *
     * @param array $data
     *
     * @return string
     */
    public static function format($data)
    {
        $value = $data['irhpPermitApplication']['relatedApplication']['licence']['organisation']['name'];
        return Escape::html($value);
    }
}
