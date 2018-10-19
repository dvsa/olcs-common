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
        $url = $sm->get('Helper\Url')->fromRoute(
            'admin-dashboard/admin-permits/permit-range',
            [
                'stockId' => $data['irhpPermitStock']['id'],
                'action' => 'edit',
                'id' => $data['id']
            ]
        );

        $permitNumber = sprintf(
            "%s%s to %s%s",
            Escape::html($data['prefix']),
            Escape::html($data['fromNo']),
            Escape::html($data['prefix']),
            Escape::html($data['toNo'])
        );

        return sprintf(
            "<a class='strong js-modal-ajax' href='%s'>%s</a>",
            $url,
            $permitNumber
        );
    }
}
