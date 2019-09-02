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

    /**
     * Create service instance
     *
     * @param TranslationHelperService $translator
     *
     * @return MultilateralNoOfPermits
     */
    public function __construct(TranslationHelperService $translator)
    {
        $this->translator = $translator;
    }

    protected function populatePermitsRequiredFieldset(
        Fieldset $permitsRequiredFieldset,
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

        $this->populateYearFieldset($permitsRequiredFieldset, $formElements);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function postProcessData(
        array $data,
        $irhpApplicationDataKey,
        $feePerPermitDataKey,
        $maxPermitsByStockDataKey
    ) {
        $data['browserTitle'] = 'permits.page.multilateral.no-of-permits.browser.title';
        $data['question'] = 'permits.page.multilateral.no-of-permits.question';

        if (isset($data[$feePerPermitDataKey])) {
            $guidanceLines = $this->generateGuidanceLines(
                $data[$feePerPermitDataKey],
                $data[$irhpApplicationDataKey]['irhpPermitApplications'],
                $data[$maxPermitsByStockDataKey]['result']
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
     *
     * @return array
     */
    protected function generateGuidanceLines(
        array $feesPerPermit,
        array $irhpPermitApplications,
        array $maxPermitsByStock
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
            $guidanceLines [] = $this->translator->translate('permits.page.multilateral.no-of-permits.permit-fees');

            foreach ($guidanceItems as $validFromYear => $feePerPermit) {
                $guidanceLines[] = $this->translator->translateReplace(
                    'permits.page.multilateral.no-of-permits.fee-per-year',
                    [$feePerPermit, $validFromYear]
                );
            }
        }

        return $guidanceLines;
    }

    protected function alterSubmitFieldsetOnMaxAllowable(Fieldset $submitFieldset)
    {
        $submitFieldset->remove('SubmitButton');
    }
}
