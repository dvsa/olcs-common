<?php

/**
 * File extension formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * File extension formatter
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class FileExtension implements FormatterInterface
{
    /**
     * Format a address
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $extPos = strrpos($data['filename'], '.');

        if ($extPos === false) {
            return '';
        }

        $extension = substr($data['filename'], $extPos + 1);
        return strtoupper($extension);
    }
}
