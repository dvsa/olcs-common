<?php

/**
 * PI Report Name formatter
 */

namespace Common\Service\Table\Formatter;

/**
 * PI Report Name formatter
 */
class PiReportName implements FormatterInterface
{
    /**
     * Format a PI Name Record
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!empty($data['pi']['case']['licence']['organisation'])) {
            // display org linked to the licence
            return OrganisationLink::format($data['pi']['case']['licence'], $column, $sm);
        } elseif (!empty($data['pi']['case']['transportManager']['homeCd']['person'])) {
            // display TM details
            return Name::format($data['pi']['case']['transportManager']['homeCd']['person']);
        }
        return '';
    }
}
