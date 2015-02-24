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
        $owner = '';

        if (!empty($data['teamName'])) {
            $owner = $data['teamName'];
        }

        $data['ownerName'] = trim($data['ownerName']);

        if (empty($data['ownerName'])) {
            $user = 'Unassigned';
        } else {
            $user = $data['ownerName'];
        }

        return $owner . ' (' . $user . ')';
    }
}
