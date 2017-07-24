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
    public static function mapFromResult(array $data)
    {
        $adPlaced = null;
        $adPlacedPost = null;
        $adPlacedLater = null;
        if (isset($data['adPlaced']) && $data['adPlaced'] === RefData::AD_UPLOAD_NOW) {
            $adPlaced = RefData::AD_UPLOAD_NOW;
        } elseif (isset($data['adPlaced']) && $data['adPlaced'] === RefData::AD_POST) {
            $adPlacedPost = RefData::AD_POST;
        } elseif (isset($data['adPlaced']) && $data['adPlaced'] === RefData::AD_UPLOAD_LATER) {
            $adPlacedLater = RefData::AD_UPLOAD_LATER;
        }
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
                'adPlaced' => $adPlaced,
                'adPlacedPost' => $adPlacedPost,
                'adPlacedLater' => $adPlacedLater,
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
            'permission' => null,
            'adPlaced' => null,
            'adPlacedIn' => null,
            'adPlacedDate' => null
        ];

        $mappedData = array_merge($mappedData, $data['data']);
        if (isset($data['advertisements'])) {
            $adv = $data['advertisements'];
            $mappedData = array_merge($mappedData, $adv);
            if (isset($adv['adPlacedNow']) && $adv['adPlacedNow'] === RefData::AD_UPLOAD_NOW) {
                $mappedData['adPlaced'] = RefData::AD_UPLOAD_NOW;
            } elseif (isset($adv['adPlacedPost']) && $adv['adPlacedPost'] === RefData::AD_POST) {
                $mappedData['adPlaced'] = RefData::AD_POST;
            } elseif (isset($adv['adPlacedLater']) && $adv['adPlacedLater'] === RefData::AD_UPLOAD_LATER) {
                $mappedData['adPlaced'] = RefData::AD_UPLOAD_LATER;
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
        $uploadNow = null;
        $uploadLater = null;
        $sendByPost = null;
        $postUploadNow = null;

        if (isset($data['advertisements']['adPlaced'])) {
            $postUploadNow = (int) $data['advertisements']['adPlaced'];
        }
        if ($postUploadNow === RefData::AD_UPLOAD_NOW) {
            $uploadNow = RefData::AD_UPLOAD_NOW;
        } elseif ($postUploadNow === RefData::AD_POST) {
            $sendByPost = RefData::AD_POST;
        } elseif ($postUploadNow === RefData::AD_UPLOAD_LATER) {
            $uploadLater = RefData::AD_UPLOAD_LATER;
        }

        // If adding a new operating centre then $data could be an empty array
        if (!isset($data['advertisements']) || !is_array($data['advertisements'])) {
            $data['advertisements'] = [];
        }

        $data['advertisements'] = array_merge(
            $data['advertisements'],
            [
                'adPlaced' => $uploadNow,
                'adPlacedLater' => $uploadLater,
                'adPlacedPost' => $sendByPost,
                'uploadedFileCount' => isset($data['advertisements']['file']['list'])
                    ? count($data['advertisements']['file']['list']) :
                    0
            ]
        );

        return $data;
    }

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
