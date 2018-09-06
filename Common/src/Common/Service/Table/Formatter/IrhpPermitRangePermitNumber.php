<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceManager;
use Common\Util\Escape;

/**
 * IRHP Permit Range table - Permit Numbers column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitRangePermitNumber implements FormatterInterface
{
    /**
     * Format
     *
     * Returns a formatted column for the Permit Numbers
     *
     * @param array          $data
     * @param array          $column
     * @param ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        return Escape::html($data['prefix']) . Escape::html($data['fromNo']) .  " to " . Escape::html($data['prefix']) . Escape::html( $data['toNo']);
    }
}
