<?php

/**
 * Internal conversation message
 */

namespace Common\Service\Table\Formatter;

use Common\Service\Helper\UrlHelperService;
use Common\Util\Escape;

/**
 * Internal conversation message
 */
class InternalConversationMessage implements FormatterPluginManagerInterface
{
    private UrlHelperService $urlHelper;
    private RefDataStatus $refDataStatus;

    /**
     * status
     *
     * @param array $row    Row data
     * @param array $column Column data
     *
     * @return     string
     * @inheritdoc
     */
    public function format($row, $column = null)
    {
        $rows ='<strong>
                    Test
                </strong>
                <br>';
        
        return $rows;
    }
}
