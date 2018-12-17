<?php


namespace Common\Data\Mapper\Licence\Surrender;

use Common\RefData;

class OperatorLicence
{
    /**
     * Map data from form to DTO
     *
     * @param array $formData Form data
     *
     * @return array
     */
    public static function mapFromForm(array $formData): array
    {
        return [
            'licenceDocumentStatus' => self::mapLicenceDocumentStatusandDetails($formData)[0],
            'licenceDocumentInfo' => self::mapLicenceDocumentStatusandDetails($formData)[1],
        ];
    }

    private static function mapLicenceDocumentStatusandDetails($formData): array
    {
        $licenceDocumentStatus = $formData['operatorLicenceDocument']['licenceDocument'];
        $licenceDocumentStatusDetails = "";

        switch ($licenceDocumentStatus) {
            case 'possession':
                $licenceDocumentStatus = RefData::SURRENDER_DOC_STATUS_DESTROYED;
                $licenceDocumentStatusDetails = null;
                break;
            case 'lost':
                $licenceDocumentStatus = RefData::SURRENDER_DOC_STATUS_LOST;
                $licenceDocumentStatusDetails = $formData['operatorLicenceDocument']['lostContent']['details'];
                break;
            case 'stolen':
                $licenceDocumentStatus = RefData::SURRENDER_DOC_STATUS_STOLEN;
                $licenceDocumentStatusDetails = $formData['operatorLicenceDocument']['stolenContent']['details'];
                break;
        }


        return [$licenceDocumentStatus, $licenceDocumentStatusDetails];
    }

    public static function mapFromApi($apiData)
    {

        $licenceDocumentStatus = $apiData["licenceDocumentStatus"]["id"];

        $formData = [];

        switch ($licenceDocumentStatus) {
            case RefData::SURRENDER_DOC_STATUS_DESTROYED:
                $formData['operatorLicenceDocument']['licenceDocument'] = RefData::SURRENDER_DOC_STATUS_DESTROYED;
                break;
            case RefData::SURRENDER_DOC_STATUS_LOST:
                $formData['operatorLicenceDocument']['licenceDocument'] = RefData::SURRENDER_DOC_STATUS_LOST;
                $formData['operatorLicenceDocument']['lostContent']['details'] = $apiData["licenceDocumentInfo"];
                break;
            case RefData::SURRENDER_DOC_STATUS_STOLEN:
                $formData['operatorLicenceDocument']['licenceDocument'] = RefData::SURRENDER_DOC_STATUS_STOLEN;
                $formData['operatorLicenceDocument']['stolenContent']['details'] = $apiData["licenceDocumentInfo"];
                break;
        }
        return $formData;
    }

}
