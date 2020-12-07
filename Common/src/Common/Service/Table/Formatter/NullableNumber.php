<?php

/**
 * Nullable Number formatter
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */

namespace Common\Service\Table\Formatter;

use Common\Util\Escape;

/**
 * Nullable Number formatter
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */
class NullableNumber implements FormatterInterface
{
    /**
     * Transforms null into 0 for display
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return int either the input number or 0 for null values
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $name = $data[$column['name']];
        if (!is_null($name)) {
            return Escape::html($name);
        }
        return 0;
    }
}
