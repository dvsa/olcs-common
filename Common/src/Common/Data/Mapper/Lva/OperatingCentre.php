<?php

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Form;
use Common\RefData;

/**
 * Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentre implements MapperInterface
{
    const VALUE_OPTION_AD_PLACED_NOW = 'adPlaced';
    const VALUE_OPTION_AD_POST = 'adSendByPost';
    const VALUE_OPTION_AD_UPLOAD_LATER = 'adPlacedLater';

    /**
     * Map from result
     *
     * @param array $data data
     *
     * @return array
     */
    public static function mapFromResult(array $data)
    {
        $adPlaceMapping = [
            RefData::AD_UPLOAD_NOW => self::VALUE_OPTION_AD_PLACED_NOW,
            RefData::AD_POST => self::VALUE_OPTION_AD_POST,
            RefData::AD_UPLOAD_LATER => self::VALUE_OPTION_AD_UPLOAD_LATER,
        ];
        $mappedData = [
            'version' => $data['version'],
            'data' => [
                'noOfVehiclesRequired' => $data['noOfVehiclesRequired'],
                'noOfTrailersRequired' => $data['noOfTrailersRequired'],
                'permission' => $data['permission'],
            ],
            'operatingCentre' => $data['operatingCentre'],
            'address' => $data['operatingCentre']['address'],
            'advertisements' => [
                'adPlacedContent' => [
                    'adPlacedIn' => $data['adPlacedIn'],
                    'adPlacedDate' => $data['adPlacedDate']
                ],
                'radio' => $adPlaceMapping[$data['adPlaced']]
            ]
        ];

        $mappedData['address']['countryCode'] = $mappedData['address']['countryCode']['id'];

        return $mappedData;
    }

    /**
     * Map from form
     *
     * @param array $data data
     *
     * @return array
     */
    public static function mapFromForm(array $data)
    {
        $mappedData = [
            'version' => $data['version'],
            'address' => isset($data['address']) ? $data['address'] : null,
            'noOfVehiclesRequired' => null,
            'noOfTrailersRequired' => null,
            'permission' => null,
            'adPlaced' => null,
            'adPlacedIn' => null,
            'adPlacedDate' => null
        ];

        $mappedData = array_merge($mappedData, $data['data']);
        if (isset($data['advertisements'])) {
            $adv = $data['advertisements'];
            $adPlaceMapping = [
                self::VALUE_OPTION_AD_PLACED_NOW => RefData::AD_UPLOAD_NOW,
                self::VALUE_OPTION_AD_POST => RefData::AD_POST,
                self::VALUE_OPTION_AD_UPLOAD_LATER => RefData::AD_UPLOAD_LATER
            ];

            if (isset($adv['radio'])) {
                $mappedData['adPlaced'] = $adPlaceMapping[$adv['radio']];
            }
            if (isset($adv['adPlacedContent']['adPlacedIn'])) {
                $mappedData['adPlacedIn'] = $adv['adPlacedContent']['adPlacedIn'];
            }
            if (isset($adv['adPlacedContent']['adPlacedDate'])) {
                $mappedData['adPlacedDate'] = $adv['adPlacedContent']['adPlacedDate'];
            }
        }

        return $mappedData;
    }

    /**
     * Map from post
     *
     * @param array $data data
     *
     * @return array
     */
    public static function mapFromPost(array $data)
    {
        if (!isset($data['advertisements']) || !is_array($data['advertisements'])) {
            $data['advertisements'] = [];
        }
        $data['advertisements']['uploadedFileCount'] =
            isset($data['advertisements']['adPlacedContent']['file']['list'])
                ? count($data['advertisements']['adPlacedContent']['file']['list'])
                : 0;

        return $data;
    }

    /**
     * Map from errors
     *
     * @param Form                        $form        form
     * @param array                       $errors      errors
     * @param FlashMessengerHelperService $fm          flash messenger helper
     * @param TranslationHelperService    $translator  translator service
     * @param string                      $location    location
     * @param string                      $taGuidesUrl guides url
     *
     * @return void
     */
    public static function mapFormErrors(
        Form $form,
        array $errors,
        FlashMessengerHelperService $fm,
        TranslationHelperService $translator,
        $location,
        $taGuidesUrl
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

        if (isset($errors['file'])) {

            foreach ($errors['file'] as $key => $message) {
                $formMessages['advertisements']['file']['upload'][] = $message;
            }

            unset($errors['file']);
        }

        if (isset($errors['postcode'])) {

            foreach ($errors['postcode'] as $key => $message) {

                foreach ($message as $k => $v) {
                    if ($k === 'ERR_OC_PC_TA_GB') {
                        $message[$k] = $translator->translateReplace($k, [$taGuidesUrl]);
                    } elseif ($k === 'ERR_TA_GOODS' || $k === 'ERR_TA_PSV' || $k === 'ERR_TA_PSV_SR') {
                        $message[$k] = $translator->translateReplace($k .'_'. strtoupper($location), [$v]);
                    }
                }

                $formMessages['address']['postcode'][] = $message;
            }

            unset($errors['postcode']);
        }

        if (isset($errors['permission'])) {

            foreach ($errors['permission'] as $key => $message) {
                $formMessages['data']['permission'][] = $message;
            }

            unset($errors['permission']);
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
