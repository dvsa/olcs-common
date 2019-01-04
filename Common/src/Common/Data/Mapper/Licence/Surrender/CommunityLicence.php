<?php


namespace Common\Data\Mapper\Licence\Surrender;

use Common\Data\Mapper\MapperInterface;
use Common\RefData;

class CommunityLicence implements MapperInterface
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
        $mappedData = static::mapCommunityLicenceDocumentStatusandDetails($formData);
        return [
            'communityLicenceDocumentStatus' => $mappedData[0],
            'communityLicenceDocumentInfo' => $mappedData[1],
        ];
    }

    private static function mapCommunityLicenceDocumentStatusandDetails($formData): array
    {
        $communityLicenceDocumentStatus = $formData['communityLicence']['communityLicenceDocument'];
        $communityLicenceDocumentStatusDetails = "";

        switch ($communityLicenceDocumentStatus) {
            case 'possession':
                $communityLicenceDocumentStatus = RefData::SURRENDER_DOC_STATUS_DESTROYED;
                $communityLicenceDocumentStatusDetails = null;
                break;
            case 'lost':
                $communityLicenceDocumentStatus = RefData::SURRENDER_DOC_STATUS_LOST;
                $communityLicenceDocumentStatusDetails = $formData['communityLicence']['lostContent']['details'];
                break;
            case 'stolen':
                $communityLicenceDocumentStatus = RefData::SURRENDER_DOC_STATUS_STOLEN;
                $communityLicenceDocumentStatusDetails = $formData['communityLicence']['stolenContent']['details'];
                break;
        }


        return [$communityLicenceDocumentStatus, $communityLicenceDocumentStatusDetails];
    }

    public static function mapFromResult(array $data)
    {
        $licenceDocumentStatus = $data["communityLicenceDocumentStatus"]["id"];

        $formData = [];

        switch ($licenceDocumentStatus) {
            case RefData::SURRENDER_DOC_STATUS_DESTROYED:
                $formData['communityLicence']['communityLicenceDocument'] = 'possession';
                break;
            case RefData::SURRENDER_DOC_STATUS_LOST:
                $formData['communityLicence']['communityLicenceDocument'] = 'lost';
                $formData['communityLicence']['lostContent']['details'] = $data["communityLicenceDocumentInfo"];
                break;
            case RefData::SURRENDER_DOC_STATUS_STOLEN:
                $formData['communityLicence']['communityLicenceDocument'] = 'stolen';
                $formData['communityLicence']['stolenContent']['details'] = $data["communityLicenceDocumentInfo"];
                break;
        }
        return $formData;
    }
}
