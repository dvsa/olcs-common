<?php

/**
 * Form Collection wrapper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\FormCollection as ZendFormCollection;
use Common\Form\Elements\Types\PostcodeSearch;

/**
 * Form Collection wrapper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormCollection extends ZendFormCollection
{
    /**
     * Hint format
     *
     * @var string
     */
    private $hintFormat = "<p class=\"hint\">%s</p>";

    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {

        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $hint = $element->getOption('hint');

        if (!empty($hint)) {
            $view = $this->getView();
            $hint = sprintf($this->hintFormat, $view->translate($hint));
        }

        $attributes       = $element->getAttributes();
        $markup           = '';
        $templateMarkup   = '';
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper    = $this->getElementHelper();
        $fieldsetHelper   = $this->getFieldsetHelper();

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = $this->renderTemplate($element);
        }

        foreach ($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $fieldsetHelper($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset);
            }
        }

        // If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
        if (!empty($templateMarkup)) {
            $markup .= $templateMarkup;
        }

        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap && $element->getOption('wrapElements') !== false) {
            $label = $element->getLabel();
            $legend = '';

            if (!empty($label)) {

                if (null !== ($translator = $this->getTranslator())) {
                    $label = $translator->translate(
                        $label,
                        $this->getTranslatorTextDomain()
                    );
                }

                $label = $escapeHtmlHelper($label);

                $legend = sprintf(
                    '<legend>%s</legend>',
                    $label
                );
            }

            $attributesString = $this->createAttributesString($attributes);
            if (!empty($attributesString)) {
                $attributesString = ' ' . $attributesString;
            }

            $markup = sprintf(
                '<fieldset%s>%s%s%s</fieldset>',
                $attributesString,
                $legend,
                $hint,
                $markup
            );
        }

        if (! ($element instanceof PostcodeSearch)) {

            return $markup;
        }

        $messages = $element->getMessages();

        if (empty($messages)) {
            return $markup;
        }

        $errorMessages = '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';

        return sprintf('<div class="validation-wrapper">%s%s</div>', $errorMessages, $markup);
    }
}
