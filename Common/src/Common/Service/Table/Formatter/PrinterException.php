<?php

/**
 * Printer Exception formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Printer Exception formatter
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PrinterException implements FormatterInterface
{
    /**
     * @param array $data The row data.
     * @param array $column The column
     * @param null $sm The service manager
     *
     * @return string
     */
    public static function format($data, $column = [], $sm = null)
    {
        if (!$data['user']) {
            $exception = $data['team']['name'];
        } else {
            $exception = isset($data['user']['contactDetails']['person']['forename']) &&
            isset($data['user']['contactDetails']['person']['familyName']) ?
                $data['user']['contactDetails']['person']['forename'] . ' ' .
                $data['user']['contactDetails']['person']['familyName'] : $data['user']['loginId'];
        }
        return $exception;
    }
}
