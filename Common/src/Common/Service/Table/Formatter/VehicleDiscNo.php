<?php

/**
 * Vehicle Disc No
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Service\Table\Formatter;

/**
 * Vehicle Disc No
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleDiscNo implements FormatterInterface
{
    public static function format($data, $column = array(), $sm = null)
    {
        if (self::isDiscPending($data)) {
            return 'Pending';
        }

        if (isset($data['goodsDiscs']) && !empty($data['goodsDiscs'])) {
            $currentDisc = $data['goodsDiscs'][0];

            return $currentDisc['discNo'];
        }

        return '';
    }

    /**
     * Check if the disc is pending
     *
     * @param array $licenceVehicleData
     * @return boolean
     */
    public static function isDiscPending($licenceVehicleData)
    {
        if (empty($licenceVehicleData['specifiedDate']) && empty($licenceVehicleData['removalDate'])) {
            return true;
        }

        if (isset($licenceVehicleData['goodsDiscs']) && !empty($licenceVehicleData['goodsDiscs'])) {
            $currentDisc = $licenceVehicleData['goodsDiscs'][0];

            if (empty($currentDisc['ceasedDate']) && empty($currentDisc['discNo'])) {

                return true;
            }
        }

        return false;
    }
}
