<?php

namespace Common\Data\Mapper\Permits;

use Common\Form\Elements\Custom\NoOfPermits as NoOfPermitsElement;
use Common\Form\Elements\Types\Html;
use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Zend\Form\Fieldset;
use RuntimeException;

/**
 * Abstract no of permits mapper
 */
abstract class AbstractNoOfPermits
{
    const PERMIT_TYPE_ID = 'changeMe';
    const TRANSLATION_KEY_PERMIT_TYPE = 'changeMe';

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
        if ($irhpPermitTypeId != static::PERMIT_TYPE_ID) {
            throw new RuntimeException('Permit type ' . $irhpPermitTypeId . ' is not supported by this mapper');
        }

        $irhpPermitApplications = $irhpApplication['irhpPermitApplications'];
        $maxPermitsByStock = $data[$irhpMaxPermitsByStockDataKey]['result'];

        $permitsRequiredFieldset = new Fieldset('permitsRequired');
        static::populatePermitsRequiredFieldset(
            $permitsRequiredFieldset,
            $translator,
            $irhpPermitApplications,
            $maxPermitsByStock,
            $irhpApplication['licence']['totAuthVehicles']
        );

        $fieldset = new Fieldset('fields');
        $fieldset->add($permitsRequiredFieldset);
        $form->add($fieldset);

        $data = static::postProcessData($data, $translator, $irhpApplicationDataKey, $feePerPermitDataKey);

        if (static::getTotalMaxPermits($irhpPermitApplications, $maxPermitsByStock) == 0) {
            $data = static::applyMaxAllowableChanges($data, $form);
        }

        return $data;
    }

    /**
     * Gets the total number of permits of this type that can be applied for
     *
     * @param array $irhpPermitApplications
     * @param array $maxPermitsByStock
     *
     * @return int
     */
    protected static function getTotalMaxPermits(array $irhpPermitApplications, array $maxPermitsByStock)
    {
        $totalMaxPermits = 0;

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $irhpPermitStockId = $irhpPermitApplication['irhpPermitWindow']['irhpPermitStock']['id'];
            $totalMaxPermits += $maxPermitsByStock[$irhpPermitStockId];
        }

        return $totalMaxPermits;
    }

    /**
     * Populates a fieldset object with form elements in accordance with permit availabilty
     *
     * @param Fieldset $fieldset
     * @param array $years
     * @param TranslationHelperService $translator
     */
    protected static function populateYearFieldset(
        Fieldset $fieldset,
        array $years,
        TranslationHelperService $translator
    ) {
        foreach ($years as $yearAttributes) {
            if ($yearAttributes['maxPermits'] > 0) {
                $element = static::createNoOfPermitsElement($yearAttributes, $translator);
            } else {
                $element = static::createHtmlElement($yearAttributes, $translator);
            }

            $fieldset->add($element);
        }
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
            'permits.page.' . static::TRANSLATION_KEY_PERMIT_TYPE . '.no-of-permits.for-year',
            [$validFromYear]
        );

        $element = new NoOfPermitsElement(
            $validFromYear,
            ['label' => $label]
        );

        switch ($issuedPermits) {
            case 0:
                $hint = $translator->translateReplace(
                    'permits.page.no-of-permits.none-issued',
                    [$maxPermits]
                );
                break;
            case 1:
                $hint = $translator->translateReplace(
                    'permits.page.no-of-permits.one-issued',
                    [$maxPermits]
                );
                break;
            default:
                $hint = $translator->translateReplace(
                    'permits.page.no-of-permits.multiple-issued',
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
            'permits.page.' . static::TRANSLATION_KEY_PERMIT_TYPE . '.no-of-permits.all-issued',
            [
                $yearAttributes['validFromYear'],
                $yearAttributes['issuedPermits']
            ]
        );

        $element->setValue('<p class="no-more-available">' . $translated . '</p>');
        return $element;
    }

    /**
     * Apply changes to the data and form to reflect the fact that no more permits can be applied for
     *
     * @param array $data
     * @param mixed $form
     *
     * @return array
     */
    protected static function applyMaxAllowableChanges(array $data, $form)
    {
        $data['browserTitle'] = sprintf(
            'permits.page.%s.no-of-permits.maximum-authorised.browser.title',
            static::TRANSLATION_KEY_PERMIT_TYPE
        );

        $data['question'] = sprintf(
            'permits.page.%s.no-of-permits.maximum-authorised.question',
            static::TRANSLATION_KEY_PERMIT_TYPE
        );

        $data['additionalGuidance'] = [];

        $data['guidance'] = [
            'value' => sprintf(
                'permits.page.%s.no-of-permits.maximum-authorised.guidance',
                static::TRANSLATION_KEY_PERMIT_TYPE
            ),
            'disableHtmlEscape' => true
        ];

        $formFieldsets = $form->getFieldsets();

        // 'Submit' fieldset isn't present when called from internal
        if (isset($formFieldsets['Submit'])) {
            $submitFieldset = $formFieldsets['Submit'];
            static::alterSubmitFieldsetOnMaxAllowable($submitFieldset);

            $submitFieldsetElements = $submitFieldset->getElements();
            $saveAndReturnButtonElement = $submitFieldsetElements['SaveAndReturnButton'];
            $saveAndReturnButtonElement->setName('CancelButton');
            $saveAndReturnButtonElement->setValue('permits.page.no-of-permits.button.cancel');
        }

        return $data;
    }

    /**
     * Populate the fieldset with the set of fields required for this permit type
     *
     * @param Fieldset $permitsRequiredFieldset
     * @param TranslationHelperService $translator
     * @param array $irhpPermitApplications
     * @param array $maxPermitsByStock
     * @param int $totAuthVehicles
     */
    abstract protected static function populatePermitsRequiredFieldset(
        Fieldset $permitsRequiredFieldset,
        TranslationHelperService $translator,
        array $irhpPermitApplications,
        array $maxPermitsByStock,
        $totAuthVehicles
    );

    /**
     * Perform any changes to the data specific to this permit type
     *
     * @param array $data
     * @param TranslationHelperService $translator
     * @param string $irhpApplicationDataKey
     * @param string $feePerPermitDataKey
     *
     * @return array
     */
    abstract protected static function postProcessData(
        array $data,
        TranslationHelperService $translator,
        $irhpApplicationDataKey,
        $feePerPermitDataKey
    );

    /**
     * Make permit type specific changes to the Submit fieldset in the event that no more permits can be applied for
     *
     * @param Fieldset $submitFieldset
     */
    abstract protected static function alterSubmitFieldsetOnMaxAllowable(Fieldset $submitFieldset);
}
