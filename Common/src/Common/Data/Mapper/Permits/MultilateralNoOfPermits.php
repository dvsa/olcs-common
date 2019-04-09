<?php

namespace Common\Data\Mapper\Permits;

use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Fieldset;

/**
 * Multilateral no of permits mapper
 */
class MultilateralNoOfPermits extends AbstractNoOfPermits
{
    const PERMIT_TYPE_ID = RefData::IRHP_MULTILATERAL_PERMIT_TYPE_ID;
    const TRANSLATION_KEY_PERMIT_TYPE = 'multilateral';

    protected static function populatePermitsRequiredFieldset(
        Fieldset $permitsRequiredFieldset,
        TranslationHelperService $translator,
        array $irhpPermitApplications,
        array $maxPermitsByStock,
        $totAuthVehicles
    ) {
        $formElements = [];

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $irhpPermitStock = $irhpPermitApplication['irhpPermitWindow']['irhpPermitStock'];
            $validFromTimestamp = strtotime($irhpPermitStock['validFrom']);
            $stockId = $irhpPermitStock['id'];

            $maxPermits = $maxPermitsByStock[$stockId];
            $validFromYear = date('Y', $validFromTimestamp);

            $formElements[$validFromYear] = [
                'validFromYear' => $validFromYear,
                'permitsRequired' => $irhpPermitApplication['permitsRequired'],
                'maxPermits' => $maxPermits,
                'issuedPermits' => $totAuthVehicles - $maxPermits,
            ];
        }

        ksort($formElements);

        self::populateYearFieldset($permitsRequiredFieldset, $formElements, $translator);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected static function postProcessData(
        array $data,
        TranslationHelperService $translator,
        $irhpApplicationDataKey,
        $feePerPermitDataKey
    ) {
        $data['browserTitle'] = 'permits.page.multilateral.no-of-permits.browser.title';
        $data['question'] = 'permits.page.multilateral.no-of-permits.question';

        $totAuthVehicles = $data[$irhpApplicationDataKey]['licence']['totAuthVehicles'];
        $data['guidance'] = [
            'value' => $translator->translateReplace(
                'permits.page.multilateral.no-of-permits.guidance',
                [$totAuthVehicles]
            ),
            'disableHtmlEscape' => true
        ];

        return $data;
    }

    protected static function alterSubmitFieldsetOnMaxAllowable(Fieldset $submitFieldset)
    {
        $submitFieldset->remove('SubmitButton');
    }
}
