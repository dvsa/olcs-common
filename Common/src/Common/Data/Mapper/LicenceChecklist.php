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
            ]
        ];
    }
}
