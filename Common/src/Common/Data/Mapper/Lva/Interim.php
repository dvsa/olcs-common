<?php

/**
 * Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Helper\FlashMessengerHelperService;
use Zend\Form\Form;

/**
 * Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Interim implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        return [
            'version' => $data['version'],
            'data' => [
                'interimReason' => $data['interimReason'],
                'interimStart' => $data['interimStart'],
                'interimEnd' => $data['interimEnd'],
                'interimAuthVehicles' => $data['interimAuthVehicles'],
                'interimAuthTrailers' => $data['interimAuthTrailers']
            ],
            'requested' => [
                'interimRequested' => (empty($data['interimStatus']['id']) ? 'N' : 'Y')
            ],
            'interimStatus' => [
                'status' => $data['interimStatus']['id']
            ]
        ];
    }

    public static function mapFromForm(array $data)
    {
        $defaultDataData = [
            'version' => null,
            'interimReason' => null,
            'interimStart' => null,
            'interimEnd' => null,
            'interimAuthVehicles' => null,
            'interimAuthTrailers' => null
        ];
        $dataData = array_merge($defaultDataData, $data['data']);

        return [
            'version' => $data['version'],
            'requested' => $data['requested']['interimRequested'],
            'reason' => $dataData['interimReason'],
            'startDate' => $dataData['interimStart'],
            'endDate' => $dataData['interimEnd'],
            'authVehicles' => (int)$dataData['interimAuthVehicles'],
            'authTrailers' => (int)$dataData['interimAuthTrailers'],
            'operatingCentres' => isset($data['operatingCentres']['id']) ? $data['operatingCentres']['id'] : null,
            'vehicles' => isset($data['vehicles']['id']) ? $data['vehicles']['id'] : null,
            'status' => isset($data['interimStatus']['status']) ? $data['interimStatus']['status'] : null
        ];
    }

    public static function mapFormErrors(Form $form, array $errors, FlashMessengerHelperService $fm)
    {
        $formMessages = [];

        if (isset($errors['reason'])) {

            foreach ($errors['reason'] as $key => $message) {
                $formMessages['data']['interimReason'][] = $message;
            }

            unset($errors['reason']);
        }

        if (isset($errors['startDate'])) {

            foreach ($errors['startDate'] as $key => $message) {
                $formMessages['data']['interimStart'][] = $message;
            }

            unset($errors['startDate']);
        }

        if (isset($errors['endDate'])) {

            foreach ($errors['endDate'] as $key => $message) {
                $formMessages['data']['interimEnd'][] = $message;
            }

            unset($errors['endDate']);
        }

        if (isset($errors['authVehicles'])) {

            foreach ($errors['authVehicles'] as $key => $message) {
                $formMessages['data']['interimAuthVehicles'][] = $message;
            }

            unset($errors['authVehicles']);
        }

        if (isset($errors['authTrailers'])) {

            foreach ($errors['authTrailers'] as $key => $message) {
                $formMessages['data']['interimAuthTrailers'][] = $message;
            }

            unset($errors['authTrailers']);
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
