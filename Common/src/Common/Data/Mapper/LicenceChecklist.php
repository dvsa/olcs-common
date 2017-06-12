<?php

namespace Common\Data\Mapper;

use Common\Service\Helper\TranslationHelperService;
use Common\RefData;

/**
 * Licence Checklist
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceChecklist
{
    /**
     * Map from result to view
     *
     * @param array                    $data       data
     * @param TranslationHelperService $translator translator
     *
     * @return array
     */
    public static function mapFromResultToView(array $data, TranslationHelperService $translator)
    {
        return [
            'typeOfLicence' => [
                'operatingFrom' =>
                    $data['trafficArea']['id'] === RefData::NORTHERN_IRELAND_TRAFFIC_AREA_CODE
                        ? $translator->translate('continuations.type-of-licence.ni')
                        : $translator->translate('continuations.type-of-licence.gb'),
                'goodsOrPsv' => $data['goodsOrPsv']['description'],
                'licenceType' => $data['licenceType']['description']
            ],
            'businessType' => [
                'typeOfBusiness' => $data['organisation']['type']['description']
            ],
            'businessDetails' => self::mapBusinessDetails($data, $translator)
        ];
    }

    /**
     * Map business details
     *
     * @param array                    $data       data
     * @param TranslationHelperService $translator translator
     *
     * @return array
     */
    private static function mapBusinessDetails($data, TranslationHelperService $translator)
    {
        $organisation = $data['organisation'];
        $organisationType = $organisation['type'];
        $businessDetails = [];
        $baseCompanyTypes = [
            RefData::ORG_TYPE_REGISTERED_COMPANY,
            RefData::ORG_TYPE_LLP,
            RefData::ORG_TYPE_PARTNERSHIP,
            RefData::ORG_TYPE_OTHER,
        ];
        $limitedCompanyTypes = [
            RefData::ORG_TYPE_REGISTERED_COMPANY,
            RefData::ORG_TYPE_LLP
        ];
        $organisationLabels = [
            RefData::ORG_TYPE_REGISTERED_COMPANY =>
                $translator->translate('continuations.business-details.company-name'),
            RefData::ORG_TYPE_LLP =>
                $translator->translate('continuations.business-details.company-name'),
            RefData::ORG_TYPE_PARTNERSHIP =>
                $translator->translate('continuations.business-details.partnership-name'),
            RefData::ORG_TYPE_OTHER =>
                $translator->translate('continuations.business-details.organisation-name')
        ];
        if (in_array($organisationType['id'], $baseCompanyTypes)) {
            $businessDetails['companyName'] = $organisation['name'];
            $businessDetails['organisationLabel'] = $organisationLabels[$organisationType['id']];
        }
        if ($organisationType['id'] !== RefData::ORG_TYPE_OTHER) {
            $businessDetails['tradingNames'] = count($data['tradingNames']) !== 0
                ? implode(', ', array_column($data['tradingNames'], 'name'))
                : $translator->translate('continuations.business-details.trading-names.none-added');
        }
        if (in_array($organisationType['id'], $limitedCompanyTypes)) {
            $businessDetails['companyNumber'] = $organisation['companyOrLlpNo'];
        }
        return $businessDetails;
    }
}
