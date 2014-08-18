<?php

/**
 * Formatter interface
 *
 * Defines the interface for table cell formatters
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
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
     * Format an cell
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public static function format($data, $column = array(), $sm = null);
}
