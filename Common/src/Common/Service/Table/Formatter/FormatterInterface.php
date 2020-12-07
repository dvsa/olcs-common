<?php

namespace Common\Service\Table\Formatter;

/**
 * Formatter interface
 *
 * Defines the interface for table cell formatters
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface FormatterInterface
{

    /**
     * Format a cell
     *
     * @param array $data
     * @param array $column [OPTIONAL]
     * @param \Laminas\ServiceManager\ServiceManager $sm [OPTIONAL]
     */
    public static function format($data/*, $column = array(), $sm = null*/);
}
