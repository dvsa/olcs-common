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
class TaskOwner implements FormatterPluginManagerInterface
{
    /**
     * Format a task owner
     *
     * @param  array $data
     * @param  array $column
     * @return string
     */
    public function format($data, $column = [])
    {
        $owner = '';

        if (!empty($data['teamName'])) {
            $owner = $data['teamName'] . ' ';
        }

        // trim leading/trailing spaces and commas
        $data['ownerName'] = trim($data['ownerName'], ' ,');

        if (empty($data['ownerName'])) {
            $user = 'Unassigned';
        } else {
            $user = $data['ownerName'];
        }

        return $owner . '(' . $user . ')';
    }
}
