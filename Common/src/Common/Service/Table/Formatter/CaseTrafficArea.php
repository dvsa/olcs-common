<?php

namespace Common\Service\Table\Formatter;

/**
 * @author Dmitry Golubev <d.e.golubev@gmail.com>
 */
class CaseTrafficArea implements FormatterInterface
{
    const NOT_APPLICABLE = 'NA';

    /**
     * Return traffic area name
     *
     * @param array                               $data   Data
     * @param array                               $column Column data
     * @param \Zend\ServiceManager\ServiceManager $sm     Service manager
     *
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $taData = [
            (isset($data['licence']['trafficArea']['name']) ? $data['licence']['trafficArea']['name'] : null),
            self::NOT_APPLICABLE,
        ];

        return current(array_filter($taData));
    }
}
