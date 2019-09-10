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

    /** @var TranslationHelperService */
    protected $translator;

    /**
     * @param array $data
     * @param $form
     * @param string $irhpApplicationDataKey
     * @param string $maxPermitsByStockDataKey
     * @param string $feePerPermitDataKey
     *
     * @return array
     */
    public function mapForFormOptions(
        array $data,
        $form,
        $irhpApplicationDataKey,
        $maxPermitsByStockDataKey,
        $feePerPermitDataKey
    ) {
        $irhpApplication = $data[$irhpApplicationDataKey];

        $irhpPermitTypeId = $irhpApplication['irhpPermitType']['id'];
        if ($irhpPermitTypeId != static::PERMIT_TYPE_ID) {
            throw new RuntimeException('Permit type ' . $irhpPermitTypeId . ' is not supported by this mapper');
        }

        $irhpPermitApplications = $irhpApplication['irhpPermitApplications'];
        $maxPermitsByStock = $data[$maxPermitsByStockDataKey]['result'];

        $permitsRequiredFieldset = new Fieldset('permitsRequired');
        $this->populatePermitsRequiredFieldset(
            $permitsRequiredFieldset,
            $irhpPermitApplications,
            $maxPermitsByStock,
            $irhpApplication['licence']['totAuthVehicles']
        );

        $fieldset = new Fieldset('fields');
        $fieldset->add($permitsRequiredFieldset);
        $form->add($fieldset);

        $data = $this->postProcessData(
            $data,
            $irhpApplicationDataKey,
            $feePerPermitDataKey,
            $maxPermitsByStockDataKey
        );

        $availableStockCount = $this->getAvailableStockCount($irhpPermitApplications, $maxPermitsByStock);

        if ($availableStockCount > 1) {
            $data['banner'] = 'permits.page.no-of-permits.banner';
        }

        if ($availableStockCount == 0) {
            $data = $this->applyMaxAllowableChanges($data, $form);
        }

        return $data;
    }

    /**
     * Populates a fieldset object with form elements in accordance with permit availabilty
     *
     * @param Fieldset $fieldset
     * @param array $years
     */
    protected function populateYearFieldset(Fieldset $fieldset, array $years)
    {
        foreach ($years as $yearAttributes) {
            if ($yearAttributes['maxPermits'] > 0) {
                $element = $this->createNoOfPermitsElement($yearAttributes);
            } else {
                $element = $this->createHtmlElement($yearAttributes);
            }

            $fieldset->add($element);
        }
    }

    /**
     * Creates and returns a NoOfPermitsElement object corresponding to the provided year attributes
     *
     * @param array $yearAttributes
     *
     * @return NoOfPermitsElement
     */
    private function createNoOfPermitsElement(array $yearAttributes): NoOfPermitsElement
    {
        $validFromYear = $yearAttributes['validFromYear'];
        $maxPermits = $yearAttributes['maxPermits'];
        $issuedPermits = $yearAttributes['issuedPermits'];

        $label = $this->translator->translateReplace(
            'permits.page.' . static::TRANSLATION_KEY_PERMIT_TYPE . '.no-of-permits.for-year',
            [$validFromYear]
        );

        $element = new NoOfPermitsElement(
            $validFromYear,
            ['label' => $label]
        );

        switch ($issuedPermits) {
            case 0:
                $hint = $this->translator->translateReplace(
                    'permits.page.no-of-permits.none-issued',
                    [$maxPermits]
                );
                break;
            case 1:
                $hint = $this->translator->translateReplace(
                    'permits.page.no-of-permits.one-issued',
                    [$maxPermits]
                );
                break;
            default:
                $hint = $this->translator->translateReplace(
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
     *
     * @return Html
     */
    private function createHtmlElement(array $yearAttributes): Html
    {
        $element = new Html($yearAttributes['validFromYear']);

        $translated = $this->translator->translateReplace(
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
    protected function applyMaxAllowableChanges(array $data, $form)
    {
        $data['browserTitle'] = sprintf(
            'permits.page.%s.no-of-permits.maximum-authorised.browser.title',
            static::TRANSLATION_KEY_PERMIT_TYPE
        );

        $data['question'] = sprintf(
            'permits.page.%s.no-of-permits.maximum-authorised.question',
            static::TRANSLATION_KEY_PERMIT_TYPE
        );

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
            $this->alterSubmitFieldsetOnMaxAllowable($submitFieldset);

            $submitFieldsetElements = $submitFieldset->getElements();
            $saveAndReturnButtonElement = $submitFieldsetElements['SaveAndReturnButton'];
            $saveAndReturnButtonElement->setName('CancelButton');
            $saveAndReturnButtonElement->setValue('permits.page.no-of-permits.button.cancel');
        }

        return $data;
    }

    /**
     * Gets the total number of stocks available for entry (i.e. the number of visible input boxes)
     *
     * @param array $irhpPermitApplications
     * @param array $maxPermitsByStock
     *
     * @return int
     */
    protected function getAvailableStockCount(array $irhpPermitApplications, array $maxPermitsByStock)
    {
        $availableStockCount = 0;

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $stockId = $irhpPermitApplication['irhpPermitWindow']['irhpPermitStock']['id'];
            $maxPermits = $maxPermitsByStock[$stockId];

            if ($maxPermits > 0) {
                $availableStockCount++;
            }
        }

        return $availableStockCount;
    }

    /**
     * Populate the fieldset with the set of fields required for this permit type
     *
     * @param Fieldset $permitsRequiredFieldset
     * @param array $irhpPermitApplications
     * @param array $maxPermitsByStock
     * @param int $totAuthVehicles
     */
    abstract protected function populatePermitsRequiredFieldset(
        Fieldset $permitsRequiredFieldset,
        array $irhpPermitApplications,
        array $maxPermitsByStock,
        $totAuthVehicles
    );

    /**
     * Perform any changes to the data specific to this permit type
     *
     * @param array $data
     * @param string $irhpApplicationDataKey
     * @param string $feePerPermitDataKey
     * @param string $maxPermitsByStockDataKey
     *
     * @return array
     */
    abstract protected function postProcessData(
        array $data,
        $irhpApplicationDataKey,
        $feePerPermitDataKey,
        $maxPermitsByStockDataKey
    );

    /**
     * Make permit type specific changes to the Submit fieldset in the event that no more permits can be applied for
     *
     * @param Fieldset $submitFieldset
     */
    abstract protected function alterSubmitFieldsetOnMaxAllowable(Fieldset $submitFieldset);
}
