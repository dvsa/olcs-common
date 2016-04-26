<?php

/**
 * Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Data\Mapper\Lva;

use Common\Data\Mapper\MapperInterface;
use Common\Service\Helper\FlashMessengerHelperService;
use Zend\Form\Form;

/**
 * Operating Centres
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentres implements MapperInterface
{
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

    public static function mapFromForm(array $data)
    {
        $mappedData = $data['data'];

        if (isset($data['dataTrafficArea'])) {
            $mappedData = array_merge($mappedData, $data['dataTrafficArea']);
        }

        return $mappedData;
    }

    public static function mapFormErrors(
        Form $form,
        array $errors,
        FlashMessengerHelperService $fm,
        $translator,
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

        if (isset($errors['trafficArea'])) {
            foreach ($errors['trafficArea'] as $taError) {
                $formMessages['dataTrafficArea']['trafficArea'][] =
                    $translator->translateReplace(key($taError) .'_'. strtoupper($location), $taError);
            }
            unset($errors['trafficArea']);
        }

        if (!empty($errors)) {

            foreach ($errors as $error) {
                $fm->addCurrentErrorMessage($error);
            }
        }

        $form->setMessages($formMessages);
    }
}
