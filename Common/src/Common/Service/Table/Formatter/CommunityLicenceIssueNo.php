<?php

/**
 * CommunityLicenceIssueNo formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * CommunityLicenceIssueNo formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommunityLicenceIssueNo implements FormatterInterface
{
    /**
     * Format the issue no field
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        return str_pad($data[$column['name']], 5, '0', STR_PAD_LEFT) .
            ($data[$column['name']] === 0 ? ' (Office copy)' : '');
    }
}
