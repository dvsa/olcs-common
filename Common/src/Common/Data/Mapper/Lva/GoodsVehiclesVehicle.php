<?php

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Table\Formatter\VehicleDiscNo;

/**
 * Goods Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsVehiclesVehicle implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        $licenceVehicle = $data;
        unset($licenceVehicle['vehicle']);

        $licenceVehicle['discNo'] = VehicleDiscNo::format($licenceVehicle);
        unset($licenceVehicle['goodsDiscs']);

        $dataFieldset = $data['vehicle'];
        $dataFieldset['version'] = $data['version'];

        return [
            'licence-vehicle' => $licenceVehicle,
            'data' => $dataFieldset
        ];
    }

    public static function mapFromErrors($errors, $form)
    {
        $dataFields = ['vrm', 'platedWeight'];
        $licenceVehicleFields = ['receivedDate', 'specifiedDate', 'removalDate', 'warningLetterSeedDate', 'discNo'];
        $formMessages = [];
        foreach ($errors as $key => $error) {
            if (in_array($key, $dataFields)) {
                foreach ($error as $subKey => $subError) {
                    $formMessages['data'][$key][] = $subError;
                }
                unset($errors[$key]);
            }
            if (in_array($key, $licenceVehicleFields)) {
                foreach ($error as $subKey => $subError) {
                    $formMessages['licenceVehicle'][$key][] = $subError;
                }
                unset($errors[$key]);
            }
        }

        $form->setMessages($formMessages);
        return $errors;
    }
}
