<?php

/**
 * Unlicensed Vehicle PSV Type formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Unlicensed Vehicle PSV Type formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class UnlicensedVehiclePsvType extends StackValue
{
    /**
     * Retrieve a nested value
     *
     * @param array $data
     * @param array $column
     * @param \Zend\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $value = parent::format($data, $column, $sm);

        if ($value) {
            $key = 'internal-operator-unlicensed-vehicles.type.'.$value;
            return $sm->get('translator')->translate($key);
        }

        return $value;
    }
}
