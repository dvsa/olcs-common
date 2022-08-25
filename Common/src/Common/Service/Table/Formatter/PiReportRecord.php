<?php

/**
 * PI Report Record formatter
 */

namespace Common\Service\Table\Formatter;

/**
 * PI Report Record formatter
 */
class PiReportRecord implements FormatterInterface
{
    /**
     * Format a PI Report Record
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!empty($data['pi']['case']['licence'])) {
            return sprintf(
                '<a class="govuk-link" href="%s">%s</a> (%s)',
                $sm->get('Helper\Url')->fromRoute(
                    'licence',
                    [
                        'licence' => $data['pi']['case']['licence']['id']
                    ]
                ),
                $data['pi']['case']['licence']['licNo'],
                $data['pi']['case']['licence']['status']['description']
            );
        } elseif (!empty($data['pi']['case']['transportManager'])) {
            return sprintf(
                '<a class="govuk-link" href="%s">TM %s</a> (%s)',
                $sm->get('Helper\Url')->fromRoute(
                    'transport-manager/details',
                    [
                        'transportManager' => $data['pi']['case']['transportManager']['id']
                    ]
                ),
                $data['pi']['case']['transportManager']['id'],
                $data['pi']['case']['transportManager']['tmStatus']['description']
            );
        }
        return '';
    }
}
