<?php

namespace Common\Data\Mapper\Permits;

use Common\Form\Elements\Custom\NoOfPermits as NoOfPermitsElement;
use Common\Form\Elements\Types\Html;
use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Fieldset;
use RuntimeException;

/**
 * No of permits mapper
 */
class NoOfPermits
{
    /**
     * @param array $data
     * @param $form
     * @param TranslationHelperService $translator
     * @param string $irhpApplicationDataKey
     * @param string $irhpMaxPermitsByStockDataKey
     * @param string $feePerPermitDataKey
     *
     * @return array
     */
    public static function mapForFormOptions(
        array $data,
        $form,
        TranslationHelperService $translator,
        $irhpApplicationDataKey,
        $irhpMaxPermitsByStockDataKey,
        $feePerPermitDataKey
    ) {
        $irhpApplication = $data[$irhpApplicationDataKey];

        $irhpPermitTypeId = $irhpApplication['irhpPermitType']['id'];
        if ($irhpPermitTypeId != RefData::IRHP_BILATERAL_PERMIT_TYPE_ID) {
            throw new RuntimeException('Permit type ' . $irhpPermitTypeId . ' is not supported by this mapper');
        }

        $maxPermitsByStock = $data[$irhpMaxPermitsByStockDataKey]['result'];
        $totAuthVehicles = $irhpApplication['licence']['totAuthVehicles'];

        $formElements = [];

        foreach ($irhpApplication['irhpPermitApplications'] as $irhpPermitApplication) {
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

                if ($countryNameA == $countryNameB) {
                    return 0;
                }

                return ($countryNameA < $countryNameB) ? -1 : 1;
            }
        );

        $permitsRequiredFieldset = new Fieldset('permitsRequired');
        foreach ($formElements as $formElement) {
            ksort($formElement['years']);

            $permitsRequiredFieldset->add(
                self::createFieldset($formElement, $translator)
            );
        }

        $fieldset = new Fieldset('fields');
        $fieldset->add($permitsRequiredFieldset);
        $form->add($fieldset);

        if (isset($data[$feePerPermitDataKey])) {
            $guidanceValue = $translator->translateReplace(
                'permits.page.bilateral.no-of-permits.guidance',
                [
                    $irhpApplication['licence']['totAuthVehicles'],
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
     * Creates and returns a Fieldset object corresponding to the provided country data
     *
     * @param array $country
     * @param TranslationHelperService $translator
     *
     * @return Fieldset
     */
    private static function createFieldset(array $country, TranslationHelperService $translator): Fieldset
    {
        $countryId = $country['id'];
        $countryName = $country['name'];
        $elementName = $countryId;

        $fieldset = new Fieldset($elementName, ['label' => $countryName]);
        foreach ($country['years'] as $yearAttributes) {
            if ($yearAttributes['maxPermits'] > 0) {
                $element = self::createNoOfPermitsElement($yearAttributes, $translator);
            } else {
                $element = self::createHtmlElement($yearAttributes, $translator);
            }

            $fieldset->add($element);
        }

        $horizontalRule = new Html($country['id'] . 'horizontalrule');
        $horizontalRule->setValue('<hr class="govuk-section-break govuk-section-break--visible">');
        $fieldset->add($horizontalRule);

        return $fieldset;
    }

    /**
     * Creates and returns a NoOfPermitsElement object corresponding to the provided year attributes
     *
     * @param array $yearAttributes
     * @param TranslationHelperService $translator
     *
     * @return NoOfPermitsElement
     */
    private static function createNoOfPermitsElement(
        array $yearAttributes,
        TranslationHelperService $translator
    ): NoOfPermitsElement {
        $validFromYear = $yearAttributes['validFromYear'];
        $maxPermits = $yearAttributes['maxPermits'];
        $issuedPermits = $yearAttributes['issuedPermits'];

        $label = $translator->translateReplace(
            'permits.page.bilateral.no-of-permits.for-year',
            [$validFromYear]
        );

        $element = new NoOfPermitsElement(
            $validFromYear,
            ['label' => $label]
        );

        switch ($issuedPermits) {
            case 0:
                $hint = $translator->translateReplace(
                    'permits.page.bilateral.no-of-permits.none-issued',
                    [$maxPermits]
                );
                break;
            case 1:
                $hint = $translator->translateReplace(
                    'permits.page.bilateral.no-of-permits.one-issued',
                    [$maxPermits]
                );
                break;
            default:
                $hint = $translator->translateReplace(
                    'permits.page.bilateral.no-of-permits.multiple-issued',
                    [$maxPermits, $issuedPermits]
                );
        }

        $element->setOptions(
            [
                'hint' => $hint,
                'hint-class' => 'govuk-hint'
            ]
        );
        $element->setValue($yearAttributes['permitsRequired']);
        $element->setAttributes(['max' => $maxPermits]);

        return $element;
    }

    /**
     * Creates and returns a Html object corresponding to the provided year attributes
     *
     * @param array $yearAttributes
     * @param TranslationHelperService $translator
     *
     * @return Html
     */
    private static function createHtmlElement(array $yearAttributes, TranslationHelperService $translator): Html
    {
        $element = new Html($yearAttributes['validFromYear']);

        $translated = $translator->translateReplace(
            'permits.page.bilateral.no-of-permits.all-issued',
            [
                $yearAttributes['validFromYear'],
                $yearAttributes['issuedPermits']
            ]
        );

        $element->setValue('<p class="no-more-available">' . $translated . '</p>');
        return $element;
    }
}
