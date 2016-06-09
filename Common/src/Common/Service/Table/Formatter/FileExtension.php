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
     * @param array $data Row data
     *
     * @return string
     */
    public static function format($data)
    {
        $extPos = strrpos($data['documentStoreIdentifier'], '.');

        if ($extPos === false) {
            return '';
        }

        $extension = substr($data['documentStoreIdentifier'], $extPos + 1);
        return strtoupper($extension);
    }
}
