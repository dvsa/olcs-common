<?php

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper\Extended;

use Common\Form\View\Helper\ApplicationContext;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\LabelAwareInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormMultiCheckbox extends \Laminas\Form\View\Helper\FormMultiCheckbox implements FactoryInterface
{
    use PrepareAttributesTrait;

    /** @var string */
    private $applicationContext;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return self
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();

        $mainConfig = $sm->get('Config');
        $this->applicationContext = $mainConfig['application_context'];

        return $this;
    }

    /**
     * Render options
     *
     * NOTE: this is predominantly a copy/paste of the corresponding method from Laminas\Form\Element\MultiCheckbox,
     * but with some minor changes to align the markup with GDS requirements
     *
     * @param  MultiCheckboxElement $element
     * @param  array                $options
     * @param  array                $selectedOptions
     * @param  array                $attributes
     * @return string
     */
    protected function renderOptions(
        MultiCheckboxElement $element,
        array $options,
        array $selectedOptions,
        array $attributes
    ) {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper      = $this->getLabelHelper();
        $labelClose       = $labelHelper->closeTag();
        $labelPosition    = $this->getLabelPosition();
        $globalLabelAttributes = [];
        $closingBracket   = $this->getInlineClosingBracket();

        if ($element instanceof LabelAwareInterface) {
            $globalLabelAttributes = $element->getLabelAttributes();
        }

        if (empty($globalLabelAttributes)) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = [];
        $count          = 0;

        $cleanElementName = preg_replace("/[^A-Za-z0-9 ]/", '', $element->getName());

        foreach ($options as $key => $optionSpec) {
            $count++;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value           = '';
            $label           = '';
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
            $selected        = (isset($inputAttributes['selected'])
                && $inputAttributes['type'] != 'radio'
                && $inputAttributes['selected']);
            $disabled        = (isset($inputAttributes['disabled']) && $inputAttributes['disabled']);

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key
                ];
            }

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

            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }

            $inputId = $cleanElementName . '-' . $count;

            $labelAttributes['for'] = $inputId;

            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;
            $inputAttributes['id'] = $inputId;

            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            $labelOpen = $labelHelper->openTag($labelAttributes);

            if ($this->applicationContext == ApplicationContext::APPLICATION_CONTEXT_SELFSERVE) {
                $template  = '<div class="govuk-checkboxes__item">%s' . $labelOpen . '%s' . $labelClose . '</div>';
            } else {
                $template  = $labelOpen . '%s%s' . $labelClose;
            }

            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $markup = sprintf($template, $label, $input);
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup = sprintf($template, $input, $label);
                    break;
            }

            $combinedMarkup[] = $markup;
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }
}
