<?php

/**
 * Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Form;

/**
 * Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentre implements MapperInterface
{
    public static function mapFromResult(array $data)
    {
        $mappedData = [
            'version' => $data['version'],
            'data' => [
                'noOfVehiclesRequired' => $data['noOfVehiclesRequired'],
                'noOfTrailersRequired' => $data['noOfTrailersRequired'],
                'sufficientParking' => $data['sufficientParking'],
                'permission' => $data['permission'],
            ],
            'operatingCentre' => $data['operatingCentre'],
            'address' => $data['operatingCentre']['address'],
            'advertisements' => [
                'adPlaced' => $data['adPlaced'],
                'adPlacedIn' => $data['adPlacedIn'],
                'adPlacedDate' => $data['adPlacedDate']
            ]
        ];

        $mappedData['address']['countryCode'] = $mappedData['address']['countryCode']['id'];

        return $mappedData;
    }

    public static function mapFromForm(array $data)
    {
        $mappedData = [
            'version' => $data['version'],
            'address' => isset($data['address']) ? $data['address'] : null,
            'noOfVehiclesRequired' => null,
            'noOfTrailersRequired' => null,
            'sufficientParking' => null,
            'permission' => null,
            'adPlaced' => null,
            'adPlacedIn' => null,
            'adPlacedDate' => null
        ];

        $mappedData = array_merge($mappedData, $data['data']);
        if (isset($data['advertisements'])) {
            $mappedData = array_merge($mappedData, $data['advertisements']);
        }

        return $mappedData;
    }

    public static function mapFormErrors(
        Form $form,
        array $errors,
        FlashMessengerHelperService $fm,
        TranslationHelperService $translator
    ) {
        $formMessages = [];

        if (isset($errors['noOfVehiclesRequired'])) {

            foreach ($errors['noOfVehiclesRequired'] as $key => $message) {
                $formMessages['data']['noOfVehiclesRequired'][] = $message;
            }

            unset($errors['noOfVehiclesRequired']);
        }

        if (isset($errors['noOfTrailersRequired'])) {

            foreach ($errors['noOfTrailersRequired'] as $key => $message) {
                $formMessages['data']['noOfTrailersRequired'][] = $message;
            }

            unset($errors['noOfTrailersRequired']);
        }

        if (isset($errors['adPlacedIn'])) {

            foreach ($errors['adPlacedIn'] as $key => $message) {
                $formMessages['advertisements']['adPlacedIn'][] = $message;
            }

            unset($errors['adPlacedIn']);
        }

        if (isset($errors['adPlacedDate'])) {

            foreach ($errors['adPlacedDate'] as $key => $message) {
                $formMessages['advertisements']['adPlacedDate'][] = $message;
            }

            unset($errors['adPlacedDate']);
        }

        if (isset($errors['postcode'])) {

            foreach ($errors['postcode'] as $key => $message) {

                foreach ($message as $k => $v) {
                    if ($k === 'ERR_OC_PC_TA_GB') {
                        $data = json_decode($v, true);
                        $message[$k] = $translator->translateReplace($k, [$data['oc'], $data['current']]);
                    }
                }

                $formMessages['address']['postcode'][] = $message;
            }

            unset($errors['postcode']);
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
