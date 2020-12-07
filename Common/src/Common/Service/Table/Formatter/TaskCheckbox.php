<?php

/**
 * Task checkbox formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Task checkbox formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TaskCheckbox implements FormatterInterface
{
    /**
     * Format a task checkbox
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     * @inheritdoc
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (isset($data['isClosed']) && $data['isClosed'] === 'Y') {
            return '';
        }

        return $sm->get('TableBuilder')->replaceContent('{{[elements/checkbox]}}', $data);
    }
}
