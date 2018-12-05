<?php

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper\Extended;

use Common\Form\View\Helper\Form;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Form\LabelAwareInterface;
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Zend\View\Renderer\PhpRenderer;

/**
 * Here we extend the View helper to allow us to add attributes that aren't in ZF2's whitelist
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormRadio extends \Zend\Form\View\Helper\FormRadio
{
    use PrepareAttributesTrait;

    protected function renderOptions(MultiCheckboxElement $element, array $options, array $selectedOptions, array $attributes)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper      = $this->getLabelHelper();
        $labelClose       = $labelHelper->closeTag();
        $labelPosition    = $this->getLabelPosition();
        $globalLabelAttributes = array();
        $closingBracket   = $this->getInlineClosingBracket();

        if ($element instanceof LabelAwareInterface) {
            $globalLabelAttributes = $element->getLabelAttributes();
        }

        if (empty($globalLabelAttributes)) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = array();
        $count          = 0;

        foreach ($options as $key => $optionSpec) {
            $count++;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value             = '';
            $label             = '';
            $hint              = '';
            $hintAttributes    = '';
            $childContent      = null;
            $inputAttributes   = $attributes;
            $labelAttributes   = $globalLabelAttributes;
            $wrapperAttributes = '';
            $selected          = (isset($inputAttributes['selected']) && $inputAttributes['type'] != 'radio' && $inputAttributes['selected']);
            $disabled          = (isset($inputAttributes['disabled']) && $inputAttributes['disabled']);

            if (is_scalar($optionSpec)) {
                $optionSpec = array(
                    'label' => $optionSpec,
                    'value' => $key
                );
            }

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }
            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }
            if (isset($optionSpec['hint_attributes'])) {
                $hintAttributes = $this->createAttributesString($optionSpec['hint_attributes']);
            }
            if (isset($optionSpec['hint'])) {
                $hint = $this->wrapWithTag($optionSpec['hint'], $hintAttributes);
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
            if (isset($optionSpec['wrapper_attributes'])) {
                $wrapperAttributes = $this->createAttributesString($optionSpec['wrapper_attributes']);
            }
            if (isset($optionSpec['childContent'])) {
                $childContent = $this->processChildContent($optionSpec);
            }

            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }

            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;

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

            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $template  = $labelOpen . '%s' . $labelClose . '%s%s';
                    $markup = sprintf($template, $label, $input, $hint);
                    break;
                case self::LABEL_APPEND:
                default:
                    $template  = '%s' . $labelOpen . '%s' . $labelClose . '%s';
                    $markup = sprintf($template, $input, $label, $hint);
                    break;
            }

            $combinedMarkup[] = $this->wrapWithTag($markup, $wrapperAttributes);

            if($childContent) {
                $combinedMarkup[] = $childContent;
            }
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }

    protected function wrapWithTag($content, $attributes = '', $tag = 'div')
    {
        return '<' . $tag . ' ' . $attributes . '>' . $content . '</' . $tag . '>';
    }

    protected function processChildContent(array $optionSpec): ? string
    {
        $childHtml = null;
        $attributes = '';
        $childContent = $optionSpec['childContent'];
        if (isset($childContent)) {
            $annotationBuilder = new AnnotationBuilder();
            $contentForm = $annotationBuilder->createForm($childContent['content']);
            $formHelper = new Form();
            $formHelper->setView($this->getView());
            $form = $formHelper->render($contentForm, false);
            if (isset($childContent['attributes'])) {
                $attributes = $this->createAttributesString($childContent['attributes']);
            }
            $childHtml = $this->wrapWithTag($form, $attributes);
        }
        return $childHtml;
    }
}
