<?php

/**
 * Fee Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\Service\Table\Formatter;

/**
 * Fee Amount formatter
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeAmount extends Money
{
    /**
     * Format a fee amount
     *
     * @param array $data
     * @param array $column
     * @param \Laminas\ServiceManager\ServiceManager $sm
     * @return string
     */
    public static function format($data, $column = array())
    {
        $amount = parent::format($data, $column);

        if (isset($data['vatAmount']) && $data['vatAmount'] > 0) {
            $amount .= '<span class="status orange">includes VAT</span>';
        }

        return $amount;
    }
}
