<?php

/**
 * Stack Value formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Service\Table\Formatter;

/**
 * Stack Value formatter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class StackValue implements FormatterInterface
{
    /**
     * Retrieve a nested value
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        if (!isset($column['stack'])) {
            throw new \InvalidArgumentException('No stack configuration found');
        }

        if (is_string($column['stack'])) {
            $column['stack'] = explode('->', $column['stack']);
        }

        return $sm->get('Helper\Stack')->getStackValue($data, $column['stack']);
    }
}
