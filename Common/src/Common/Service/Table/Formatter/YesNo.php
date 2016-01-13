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
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($column['stack'])) {
            if (is_string($column['stack'])) {
                $column['stack'] = explode('->', $column['stack']);
            }

            $value = $sm->get('Helper\Stack')->getStackValue($data, $column['stack']);
        } else {
            $value = $data[$column['name']];
        }

        return ($value == 1 || $value === 'Y' ? 'Yes' : 'No');
    }
}
