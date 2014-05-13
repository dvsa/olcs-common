<?php

/**
 * YesNo formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * YesNo formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class YesNo implements FormatterInterface
{
    /**
     * Format a address
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column, $sm)
    {
        return ($data[$column['name']] == 1 ? 'Y' : 'N');
    }
}
