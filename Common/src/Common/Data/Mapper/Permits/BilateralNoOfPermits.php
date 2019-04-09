<?php

namespace Common\Data\Mapper\Permits;

use Common\Form\Elements\Types\Html;
use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Fieldset;

/**
 * Bilateral no of permits mapper
 */
class BilateralNoOfPermits extends AbstractNoOfPermits
{
    const PERMIT_TYPE_ID = RefData::IRHP_BILATERAL_PERMIT_TYPE_ID;
    const TRANSLATION_KEY_PERMIT_TYPE = 'bilateral';

    protected static function populatePermitsRequiredFieldset(
        Fieldset $permitsRequiredFieldset,
        TranslationHelperService $translator,
        array $irhpPermitApplications,
        array $maxPermitsByStock,
        $totAuthVehicles
    ) {
        $formElements = self::generateFormElementData(
            $irhpPermitApplications,
            $maxPermitsByStock,
            $totAuthVehicles
        );

        foreach ($formElements as $formElement) {
            $permitsRequiredFieldset->add(
                self::createCountryFieldset($formElement, $translator)
            );
        }
    }

    protected static function postProcessData(
        array $data,
        TranslationHelperService $translator,
        $irhpApplicationDataKey,
        $feePerPermitDataKey
    ) {
        $data['browserTitle'] = 'permits.page.bilateral.no-of-permits.browser.title';
        $data['question'] = 'permits.page.bilateral.no-of-permits.question';

        if (isset($data[$feePerPermitDataKey])) {
            $guidanceValue = $translator->translateReplace(
                'permits.page.bilateral.no-of-permits.guidance',
                [
                    $data[$irhpApplicationDataKey]['licence']['totAuthVehicles'],
                    $data[$feePerPermitDataKey]['feePerPermit']
                ]
            );

            $data['guidance'] = [
                'value' => $guidanceValue,
                'disableHtmlEscape' => true
            ];
        }

        return $data;
    }

    /**
     * Generates a presentation-neutral representation of the fieldsets and form elements to be included in the
     * fieldset
     *
     * @param array $irhpPermitApplications
     * @param array $maxPermitsByStock
     * @param int $totAuthVehicles
     *
     * @return array
     */
    private static function generateFormElementData(
        array $irhpPermitApplications,
        array $maxPermitsByStock,
        $totAuthVehicles
    ) {
        $formElements = [];

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $irhpPermitStock = $irhpPermitApplication['irhpPermitWindow']['irhpPermitStock'];
            $validFromTimestamp = strtotime($irhpPermitStock['validFrom']);
            $country = $irhpPermitStock['country'];
            $stockId = $irhpPermitStock['id'];

            $permitsRequired = $irhpPermitApplication['permitsRequired'];
            $maxPermits = $maxPermitsByStock[$stockId];
            $validFromYear = date('Y', $validFromTimestamp);
            $countryId = $country['id'];
            $countryName = $country['countryDesc'];

            if (!isset($formElements[$countryId])) {
                $formElements[$countryId] = [
                    'name' => $countryName,
                    'id' => $countryId,
                    'years' => []
                ];
            }

            $formElements[$countryId]['years'][$validFromYear] = [
                'validFromYear' => $validFromYear,
                'permitsRequired' => $permitsRequired,
                'maxPermits' => $maxPermits,
                'issuedPermits' => $totAuthVehicles - $maxPermits,
            ];
        }

        usort(
            $formElements,
            function ($elementA, $elementB) {
                $countryNameA = $elementA['name'];
                $countryNameB = $elementB['name'];

                return ($countryNameA < $countryNameB) ? -1 : 1;
            }
        );

        foreach ($formElements as &$formElement) {
            ksort($formElement['years']);
        }

        return $formElements;
    }

    /**
     * Creates and returns a Fieldset object corresponding to the provided country data
     *
     * @param array $country
     * @param TranslationHelperService $translator
     *
     * @return Fieldset
     */
    private static function createCountryFieldset(array $country, TranslationHelperService $translator): Fieldset
    {
        $countryId = $country['id'];
        $countryName = $country['name'];
        $elementName = $countryId;

        $fieldset = new Fieldset($elementName, ['label' => $countryName]);
        self::populateYearFieldset($fieldset, $country['years'], $translator, static::TRANSLATION_KEY_PERMIT_TYPE);

        $horizontalRule = new Html($country['id'] . 'horizontalrule');
        $horizontalRule->setValue('<hr class="govuk-section-break govuk-section-break--visible">');
        $fieldset->add($horizontalRule);

        return $fieldset;
    }

    protected static function alterSubmitFieldsetOnMaxAllowable(Fieldset $submitFieldset)
    {
        $submitFieldsetElements = $submitFieldset->getElements();
        $submitButtonElement = $submitFieldsetElements['SubmitButton'];

        $submitButtonElement->setName('SelectOtherCountriesButton');
        $submitButtonElement->setValue('permits.page.bilateral.no-of-permits.button.select-other-countries');
    }
}
