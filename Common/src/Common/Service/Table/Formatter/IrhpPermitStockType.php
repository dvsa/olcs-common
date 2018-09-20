<?php

namespace Common\Service\Table\Formatter;

use Zend\ServiceManager\ServiceManager;
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
     * @param array          $data
     *
     * @return string
     */
    public static function format($data)
    {
        return Escape::html($data['irhpPermitType']['name']['description']);
    }
}
