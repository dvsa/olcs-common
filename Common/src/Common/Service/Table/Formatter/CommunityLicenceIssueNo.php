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
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if ($data[$column['name']] != '0') {
            return $data[$column['name']];
        } else {
            return $data[$column['name']] . ' (Office copy)';
        }
    }
}
