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
        $feePerPermitDataKey,
        $maxPermitsByStockDataKey
    ) {
        $data['browserTitle'] = 'permits.page.multilateral.no-of-permits.browser.title';
        $data['question'] = 'permits.page.multilateral.no-of-permits.question';

        if (isset($data[$feePerPermitDataKey])) {
            $guidanceLines = static::generateGuidanceLines(
                $data[$feePerPermitDataKey],
                $data[$irhpApplicationDataKey]['irhpPermitApplications'],
                $data[$maxPermitsByStockDataKey]['result'],
                $translator
            );

            $data['guidance'] = [
                'value' => implode('<br>', $guidanceLines),
                'disableHtmlEscape' => true
            ];
        }

        return $data;
    }

    /**
     * Returns an array, each element of which represents a single line within the guidance message. Will return an
     * empty array if the guidance message is not applicable
     *
     * @param array $feesPerPermit
     * @param array $irhpPermitApplications
     * @param array $maxPermitsByStock
     * @param TranslationHelperService $translator
     *
     * @return array
     */
    protected static function generateGuidanceLines(
        array $feesPerPermit,
        array $irhpPermitApplications,
        array $maxPermitsByStock,
        TranslationHelperService $translator
    ) {
        $guidanceItems = [];
        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $irhpPermitStock = $irhpPermitApplication['irhpPermitWindow']['irhpPermitStock'];
            $irhpPermitStockId = $irhpPermitStock['id'];
            $validFromTimestamp = strtotime($irhpPermitStock['validFrom']);
            $validFromYear = date('Y', $validFromTimestamp);

            if ($maxPermitsByStock[$irhpPermitStockId] > 0) {
                $guidanceItems[$validFromYear] = $feesPerPermit[$irhpPermitApplication['id']];
            }
        }
        ksort($guidanceItems);

        $guidanceLines = [];
        if (count($guidanceItems) > 0) {
            $guidanceLines [] = $translator->translate('permits.page.multilateral.no-of-permits.permit-fees');

            foreach ($guidanceItems as $validFromYear => $feePerPermit) {
                $guidanceLines[] = $translator->translateReplace(
                    'permits.page.multilateral.no-of-permits.fee-per-year',
                    [$feePerPermit, $validFromYear]
                );
            }
        }

        return $guidanceLines;
    }

    protected static function alterSubmitFieldsetOnMaxAllowable(Fieldset $submitFieldset)
    {
        $submitFieldset->remove('SubmitButton');
    }
}
