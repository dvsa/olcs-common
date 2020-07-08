<?php

namespace Common\Service\Qa\Custom\Bilateral;

use Common\Form\Elements\Types\Html;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Qa\FieldsetPopulatorInterface;
use Common\Service\Qa\RadioFieldsetPopulator;
use Zend\Form\Element\Hidden;
use Zend\Form\Fieldset;

class PermitUsageFieldsetPopulator implements FieldsetPopulatorInterface
{
    /** @var RadioFieldsetPopulator */
    private $radioFieldsetPopulator;

    /** @var TranslationHelperService */
    private $translator;

    /**
     * Create service instance
     *
     * @param RadioFieldsetPopulator $radioFieldsetPopulator
     * @param TranslationHelperService $translator
     *
     * @return PermitUsageFieldsetPopulator
     */
    public function __construct(
        RadioFieldsetPopulator $radioFieldsetPopulator,
        TranslationHelperService $translator
    ) {
        $this->radioFieldsetPopulator = $radioFieldsetPopulator;
        $this->translator = $translator;
    }

    /**
     * Populate the fieldset with a radio or html element based on the supplied options array
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    public function populate($form, Fieldset $fieldset, array $options)
    {
        if (count($options['options']) === 1) {
            $this->populateSingleOption($fieldset, $options['options'][0]);
            $form->get('Submit')->get('SubmitButton')->setValue('permits.button.continue');
        } else {
            $this->populateMultipleOptions($form, $fieldset, $options);
        }

        $fieldset->add(
            [
                'name' => 'warningVisible',
                'type' => Hidden::class,
                'attributes' => [
                    'value' => 0
                ]
            ]
        );
    }

    /**
     * Populate the fieldset with a html element in a single option scenario
     *
     * @param Fieldset $fieldset
     * @param array $firstOption
     */
    private function populateSingleOption(Fieldset $fieldset, array $firstOption)
    {
        $translationKey = $this->generateTranslationKey($firstOption['value'], 'single-option');

        $markup = sprintf(
            '<p class="govuk-body-l">%s</p>',
            $this->translator->translate($translationKey)
        );

        $fieldset->add(
            [
                'name' => 'qaHtml',
                'type' => Html::class,
                'attributes' => [
                    'value' => $markup,
                ]
            ]
        );

        $fieldset->add(
            [
                'name' => 'qaElement',
                'type' => Hidden::class,
                'attributes' => [
                    'value' => $firstOption['value'],
                ]
            ]
        );
    }

    /**
     * Populate the fieldset with radio buttons in a multiple option scenario
     *
     * @param mixed $form
     * @param Fieldset $fieldset
     * @param array $options
     */
    private function populateMultipleOptions($form, Fieldset $fieldset, array $options)
    {
        foreach ($options['options'] as $key => $option) {
            $options['options'][$key]['label'] = $this->generateTranslationKey($option['value'], 'multiple-options');
        }

        $this->radioFieldsetPopulator->populate($form, $fieldset, $options);
    }

    /**
     * Generate a translation key based upon an option value
     *
     * @param string $optionValue
     * @param string $usageContext
     *
     * @return string
     */
    private function generateTranslationKey($optionValue, $usageContext)
    {
        return sprintf(
            'qanda.bilaterals.permit-usage.%s.%s',
            $usageContext,
            str_replace('_', '-', $optionValue)
        );
    }
}
