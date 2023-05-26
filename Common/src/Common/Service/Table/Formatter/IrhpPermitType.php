<?php

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * IRHP Permit Type formatter
 */
class IrhpPermitType implements FormatterPluginManagerInterface
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
    public function format($data, $column = [])
    {
        // TODO: Remove ternary when ECMT permits are removed.
        $type = isset($data['irhpPermitType']) ?
            $data['irhpPermitType']['name']['description'] :
            $data['permitType']['description'];

        return Escape::html($type);
    }
}
