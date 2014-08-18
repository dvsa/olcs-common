<?php

/**
 * task identifier formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * task identifier formatter
 *
 * @author nick payne <nick.payne@valtech.co.uk>
 */
class TaskIdentifier implements FormatterInterface
{
    /**
     * Format a task identifier
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column, $sm)
    {
        $identifier = $data['identifier'];
        if ($identifier === 'Unlinked') {
            return 'Unlinked';
        }

        $value = '<a href=#>' . $data['identifier'] . '</a>';
        if ($data['licenceCount'] > 1) {
            $value .= ' (MLH)';
        }

        return $value;
    }
}
