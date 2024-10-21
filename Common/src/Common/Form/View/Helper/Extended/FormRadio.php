<?php

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Common\Form\View\Helper\Extended;

use Common\View\Helper\UniqidGenerator;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormRadio extends \Laminas\Form\View\Helper\FormRadio
{
    use PrepareAttributesTrait;

    /** @var UniqidGenerator */
    protected $idGenerator;

    /**
     * FormRadio constructor.
     */
    public function __construct(UniqidGenerator $idGenerator = null)
    {
        $this->idGenerator = $idGenerator ?? new UniqidGenerator();
    }

    protected function renderOptions(
        MultiCheckboxElement $element,
        array                $options,
        array                $selectedOptions,
        array                $attributes
    ): string
    {
        $translator = $this->getTranslator();
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $labelClose = $labelHelper->closeTag();
        $labelPosition = $this->getLabelPosition();
        $closingBracket = $this->getInlineClosingBracket();
        $radiosWrapperAttributes = $this->makeRadiosWrapperAttributes($attributes);
        $elementOptions = $element->getOptions();
        $valueOptionHints = $elementOptions['value_option_hints'] ?? [];
        $globalLabelAttributes = $element->getLabelAttributes();

        if ($globalLabelAttributes === []) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = [];
        $count = 0;

        foreach ($options as $key => $optionSpec) {
            ++$count;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value = '';
            $label = '';
            $hint = '';
            $hintAttributes = '';
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
            $itemWrapperAttributes = '';
            $selected = (isset($inputAttributes['selected']) && $inputAttributes['type'] != 'radio' && $inputAttributes['selected']);
            $disabled = (isset($inputAttributes['disabled']) && $inputAttributes['disabled']);

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key
                ];
            }

            $optionSpec = $this->addGovUkRadioStyles($optionSpec);

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }

            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }

            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }

            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }

            if (isset($optionSpec['label_attributes'])) {
                $labelAttributes = (isset($labelAttributes))
                    ? array_merge($labelAttributes, $optionSpec['label_attributes'])
                    : $optionSpec['label_attributes'];
            }

            if (isset($optionSpec['attributes'])) {
                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }

            if (isset($optionSpec['item_wrapper_attributes'])) {
                $itemWrapperAttributes = $this->createAttributesString($optionSpec['item_wrapper_attributes']);
            }

            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }

            $inputAttributes['value'] = $value;
            $inputAttributes['checked'] = $selected;
            $inputAttributes['disabled'] = $disabled;

            $hasOptionHint = isset($valueOptionHints[$key]);
            $inputAttributes = $this->maybeAddInputId($inputAttributes, $hasOptionHint);
            $labelAttributes['for'] = $inputAttributes['id'];

            if ($hasOptionHint) {
                $optionSpec['hint'] = $valueOptionHints[$key];
                $optionSpec['hint_attributes'] = [
                    'id' => $inputAttributes['aria-describedby'],
                    'class' => 'govuk-hint govuk-radios__hint',
                ];
            }

            if (isset($optionSpec['hint_attributes'])) {
                $hintAttributes = $this->createAttributesString($optionSpec['hint_attributes']);
            }

            if (isset($optionSpec['hint'])) {
                $hintText = $optionSpec['hint'];

                if (null !== $translator) {
                    $hintText = $translator->translate(
                        $hintText,
                        $this->getTranslatorTextDomain()
                    );
                }

                $hint = $this->wrapWithTag($hintText, $hintAttributes);
            }

            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );

            if (null !== $translator) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (!$element instanceof LabelAwareInterface || !$element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            $labelOpen = $labelHelper->openTag($labelAttributes);

            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $template = $labelOpen . '%s' . $labelClose . '%s%s';
                    $markup = sprintf($template, $label, $input, $hint);
                    break;
                case self::LABEL_APPEND:
                default:
                    $template = '%s' . $labelOpen . '%s' . $labelClose . '%s';
                    $markup = sprintf($template, $input, $label, $hint);
                    break;
            }

            $markup = $this->wrapWithTag($markup, $itemWrapperAttributes);

            if (isset($optionSpec['markup_before'])) {
                $markup = $optionSpec['markup_before'] . $markup;
            }

            $combinedMarkup[] = $markup;
        }

        $outputMarkup = implode($this->getSeparator(), $combinedMarkup);

        if ($outputMarkup !== '') {
            return $this->wrapWithTag(
                $outputMarkup,
                $this->createAttributesString($radiosWrapperAttributes)
            );
        }

        return $outputMarkup;
    }

    protected function wrapWithTag(string $content, string $attributes = '', string $tag = 'div'): string
    {
        return '<' . $tag . ' ' . $attributes . '>' . $content . '</' . $tag . '>';
    }

    protected function addGovUkRadioStyles(array $valueOptions): array
    {

        $gdsAttributes = [
            'item_wrapper_attributes' => [
                'class' => 'govuk-radios__item',
            ],
            'attributes' => [
                'class' => 'govuk-radios__input',
            ],
            'label_attributes' => [
                'class' => 'govuk-label govuk-radios__label',
            ],
            'hint_attributes' => [
                'class' => 'govuk-hint govuk-radios__hint',
            ],
        ];

        foreach ($gdsAttributes as $key => $attributes) {
            if (isset($valueOptions[$key]) && isset($valueOptions[$key]['class'])) {
                $valueOptions[$key]['class'] .= ' ' . $attributes['class'];
            } elseif (isset($valueOptions[$key])) {
                $valueOptions[$key] = array_merge($valueOptions[$key], $attributes);
            } else {
                $valueOptions[$key] = $attributes;
            }
        }

        return $valueOptions;
    }

    protected function maybeAddInputId(array $inputAttributes, bool $hasOptionHint): array
    {
        if (!isset($inputAttributes['id'])) {
            $inputAttributes['id'] = $this->idGenerator->generateId();
        }

        if ($hasOptionHint) {
            $inputAttributes['aria-describedby'] = $this->getItemHintId($inputAttributes['id']);
        }

        return $inputAttributes;
    }

    protected function makeRadiosWrapperAttributes(array $attributes): array
    {
        $radiosWrapperAttributes = [
            'class' => 'govuk-radios'
        ];

        if (isset($attributes['radios_wrapper_attributes'])) {
            foreach ($attributes['radios_wrapper_attributes'] as $key => $value) {
                if (isset($radiosWrapperAttributes[$key])) {
                    $radiosWrapperAttributes[$key] = $radiosWrapperAttributes[$key] . ' ' . $value;
                } else {
                    $radiosWrapperAttributes[$key] = $value;
                }
            }
        }

        return $radiosWrapperAttributes;
    }

    private function getItemHintId(string $inputId): string
    {
        return $inputId . '-item-hint';
    }
}
