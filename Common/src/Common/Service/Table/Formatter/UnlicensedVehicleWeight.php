<?php

/**
 * Unlicensed Vehicle Weight formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Unlicensed Vehicle Weight formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class UnlicensedVehicleWeight extends StackValue
{
    /**
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $value = parent::format($data, $column, $sm);

        return empty($value) ? '' : $value . ' kg';
    }
}
