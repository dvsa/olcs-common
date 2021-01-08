<?php

namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\TranslationHelperService;
use Laminas\Form\FormInterface;

/**
 * Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentres implements MapperInterface
{
    const API_ERR_KEYS = ['ERR_TA_GOODS', 'ERR_TA_PSV', 'ERR_TA_PSV_SR', 'ERR_TA_PSV_RES'];

    /**
     * Map Form and Api data
     *
     * @param array $data Api data
     *
     * @return array
     */
    public static function mapFromResult(array $data)
    {
        $enforcementArea = null;

        if (isset($data['licence']['enforcementArea']['id'])) {
            $enforcementArea = $data['licence']['enforcementArea']['id'];
        } elseif (isset($data['enforcementArea']['id'])) {
            $enforcementArea = $data['enforcementArea']['id'];
        }

        $trafficArea = null;
        if (isset($data['licence']['trafficArea']['id'])) {
            $trafficArea = $data['licence']['trafficArea']['id'];
        } elseif (isset($data['trafficArea']['id'])) {
            $trafficArea = $data['trafficArea']['id'];
        }

        return [
            'data' => $data,
            'dataTrafficArea' => [
                'trafficArea' => $trafficArea,
                'enforcementArea' => $enforcementArea,
            ]
        ];
    }

    /**
     * Map form form data
     *
     * @param array $data Form data
     *
     * @return array|mixed
     */
    public static function mapFromForm(array $data)
    {
        $mappedData = $data['data'];

        if (isset($data['dataTrafficArea'])) {
            $mappedData = array_merge($mappedData, $data['dataTrafficArea']);
        }

        return $mappedData;
    }

    /**
     * Process errors and add to form
     *
     * @param FormInterface               $form       Form
     * @param array                       $errors     List of errors from Api
     * @param FlashMessengerHelperService $fm         Flash messenger
     * @param TranslationHelperService    $translator Translator Service
     * @param string                      $location   Selfserve|Internal
     *
     * @return void
     */
    public static function mapFormErrors(
        FormInterface $form,
        array $errors,
        FlashMessengerHelperService $fm,
        TranslationHelperService $translator,
        $location
    ) {
        $formMessages = [];

        if (isset($errors['totCommunityLicences'])) {
            foreach ($errors['totCommunityLicences'] as $key => $message) {
                $formMessages['data']['totCommunityLicences'][] = $message;
            }

            unset($errors['totCommunityLicences']);
        }

        if (isset($errors['totAuthVehicles'])) {
            foreach ($errors['totAuthVehicles'] as $key => $message) {
                $formMessages['data']['totAuthVehicles'][] = $message;
            }

            unset($errors['totAuthVehicles']);
        }

        if (isset($errors['totAuthTrailers'])) {
            foreach ($errors['totAuthTrailers'] as $key => $message) {
                $formMessages['data']['totAuthTrailers'][] = $message;
            }

            unset($errors['totAuthTrailers']);
        }

        if (isset($errors['operatingCentres'])) {
            foreach ($errors['operatingCentres'] as $key => $message) {
                $formMessages['table']['table'][] = $message;
            }

            unset($errors['operatingCentres']);
        }

        if (isset($errors['enforcementArea'])) {
            foreach ($errors['enforcementArea'] as $key => $message) {
                $formMessages['dataTrafficArea']['enforcementArea'][] = $message;
            }

            unset($errors['enforcementArea']);
        }

        self::mapApiErrors($location, $errors, $fm, $translator);

        $form->setMessages($formMessages);
    }

    /**
     * Map error messages from API (not assigned to field or special)
     *
     * @param string                      $location      Selfserve|Internal
     * @param array                       $apiErrors     List of errors from Api
     * @param FlashMessengerHelperService $flashMsgsSrv  Flash messenger
     * @param TranslationHelperService    $translatorSrv Translator Service
     *
     * @return void
     */
    public static function mapApiErrors(
        $location,
        array $apiErrors,
        FlashMessengerHelperService $flashMsgsSrv,
        TranslationHelperService $translatorSrv
    ) {
        if (empty($apiErrors)) {
            return;
        }

        foreach ($apiErrors as $section => $apiErr) {
            if (!is_array($apiErr)) {
                $flashMsgsSrv->addCurrentErrorMessage($apiErr);

                continue;
            }

            foreach ($apiErr as $err) {
                if (!is_array($err)) {
                    $flashMsgsSrv->addCurrentErrorMessage($err);

                    continue;
                }

                $key = key($err);

                if (in_array($key, self::API_ERR_KEYS)) {
                    $msg = $translatorSrv->translateReplace($key .'_'. strtoupper($location), $err);
                } else {
                    $msg = current($err);
                }

                $flashMsgsSrv->addCurrentErrorMessage($msg);
            }
        }
    }
}
