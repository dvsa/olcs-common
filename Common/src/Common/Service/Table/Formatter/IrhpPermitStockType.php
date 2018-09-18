<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Util\Escape;

/**
 * IRHP Permit Stock table - Permit Type column formatter
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class IrhpPermitStockType implements FormatterInterface
{
    /**
     * Format
     *
     * Returns the title of the ECMT Permit
     *
     * @param array                     $data
     * @param array                     $column The column data.
     * @param ServiceLocatorInterface   $sm     The service manager.
     *
     * @return string
     */
    public static function format($data, $column = array(), ServiceLocatorInterface $sm = null)
    {
        unset($column);

        $url = $sm->get('Helper\Url')->fromRoute(
            'admin-dashboard/admin-permits/permits-system-settings',
            [
                'permitStockId' => $data['id'],
            ]
        );

        return sprintf(
            '<a class="strong" href="%s">%s</a>',
            $url,
            $data['irhpPermitType']['name']['description']
        );

        // return Escape::html($data['irhpPermitType']['name']['description']);
    }
}
