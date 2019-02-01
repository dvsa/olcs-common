<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * IRHP Permit Type formatter
 */
class IrhpPermitType implements FormatterInterface
{
    /**
     * Format
     *
     * Returns the IRHP Permit Type
     *
     * @param array $data
     *
     * @return string
     */
    public static function format($data)
    {
        // TODO: Remove ternary when ECMT permits are removed.
        $type = isset($data['irhpPermitType']) ?
            $data['irhpPermitType']['name']['description'] :
            $data['permitType']['description'];

        return Escape::html($type);
    }
}
