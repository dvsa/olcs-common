<?php

declare(strict_types=1);

/**
 * OcNoOfLgvVehiclesRequired.php
 */
namespace Common\Service\Table\Formatter;

/**
 * Class OcNoOfLgvVehiclesRequired
 *
 * Format results for the table.
 *
 * @package Common\Service\Table\Formatter
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class OcNoOfLgvVehiclesRequired implements FormatterInterface
{
    /**
     * Get the no of lgv vehicles required against the operating centre, or zero if no value present
     *
     * @param array $data The row data.
     * @param array $column The column data.
     * @param null $sm The service manager.
     *
     * @return mixed
     */
    public static function format($data, $column = array(), $sm = null)
    {
        $noOfLgvVehiclesRequired = $data['noOfLgvVehiclesRequired'];

        if (is_null($noOfLgvVehiclesRequired)) {
            return 0;
        }

        return $noOfLgvVehiclesRequired;
    }
}
