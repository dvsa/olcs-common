<?php

/**
 * Comment formatter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Comment formatter
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class Comment implements FormatterInterface
{
    /**
     * Comment value
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($data[$column['name']]) && !is_null($data[$column['name']])) {
            return nl2br($data[$column['name']]);
        }

        return '';
    }
}
