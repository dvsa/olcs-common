<?php

/**
 * Financial History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Data\Mapper\Lva;

/**
 * Financial History
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class FinancialHistory
{
    /**
     * Map result
     *
     * @param array $data
     *
     * @return array data
     */
    public static function mapFromResult(array $data)
    {
        return [
            'data' => $data
        ];
    }
}
