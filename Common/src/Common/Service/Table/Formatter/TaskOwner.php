<?php

/**
 * Task Owner Formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Task Owner Formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskOwner implements FormatterInterface
{
    /**
     * Format a task owner
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $ownerParts = [];

        if (!empty($data['teamName'])) {
            $ownerParts[] = $data['teamName'];
        }

        if (empty($data['ownerName'])) {
            $ownerParts[] = 'Unassigned';
        } else {
            $ownerParts[] = $data['ownerName'];
        }

        return implode(': ', $ownerParts);
    }
}
