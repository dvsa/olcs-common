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
        $type = $data['type'];

        $link = '';

        // @NOTE please bear in mind these types are completely
        // made up for the time being, and probably not realistic.
        // Don't know what the data looks like yet.
        switch ($type) {
        case 'Application':
        case 'Licence':
            $link = $data['licenceNumber'];
            break;
        case 'Transport Manager':
            $link = $data['transportManagerId'];
            break;
        case 'Case':
            $link = $data['caseId'];
            break;
        case 'Bus registration':
            $link = $data['busRegistrationNumber'];
            break;
        }

        return $link . ' (MLH)';
    }
}
