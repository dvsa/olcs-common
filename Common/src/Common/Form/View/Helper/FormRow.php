<?php

/**
 * Render form row
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\LabelAwareInterface;
use Zend\Form\View\Helper\FormRow as ZendFormRow;
use Zend\Form\ElementInterface as ZendElementInterface;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Button;
use Zend\Form\ElementInterface;
use Common\Form\Elements\InputFilters\SingleCheckbox;
use Common\Form\Elements\Types\Table;
use Common\Form\Elements\InputFilters\NoRender;
use Common\Form\Elements\InputFilters\ActionButton;
use Common\Form\Elements\InputFilters\ActionLink;

/**
 * Render form row
 *
 * @author Michael Cooper <michael.cooper@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormRow extends ZendFormRow
{

    /**
     * The form row output format.
     *
     * @var string
     */
    private static $format = '<div class="field %s">%s</div>';
    private static $errorClass = '<div class="validation-wrapper">%s</div>';
    protected $fieldsetWrapper = '<fieldset%4$s>%2$s%1$s%3$s</fieldset>';
    protected $fieldsetLabelWrapper = '<legend>%s</legend>';
    protected $fieldsetHintFormat = "<p class=\"hint\">%s</p>";

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param  ZendElementInterface $element
     * @throws \Zend\Form\Exception\DomainException
     * @return string
     */
    public function render(ZendElementInterface $element)
    {
        //$oldRenderErrors = $this->getRenderErrors();
        $oldRenderErrors = true;
        if ($oldRenderErrors) {

            /**
             * We don't want the parent class to render the errors.
             */
            $this->setRenderErrors(false);
            $elementErrors = $this->getElementErrorsHelper()->render($element);
        }

        if ($element instanceof ActionButton || $element instanceof ActionLink) {
            return $this->renderRow($element);
        }

        if ($element instanceof NoRender) {
            return '';
        }

        if ($element instanceof Table) {
            $markup = $element->render();
        } else {

            if ($element instanceof SingleCheckbox) {
                $this->labelPosition = self::LABEL_APPEND;
            }

            $renderAsFieldset = $element->getOption('render_as_fieldset');

            if ($renderAsFieldset) {
                $markup = $this->renderFieldset($element);
            } else {
                $markup = $this->renderRow($element);
            }

            if ($element instanceof SingleCheckbox) {
                $this->labelPosition = self::LABEL_PREPEND;
            }
        }

        $wrap = true;

        $type = $element->getAttribute('type');
        if ($type === 'multi_checkbox' || $type === 'radio'
            || $element->getAttribute('id') == 'security') {
            $wrap = false;
        }

        if ($oldRenderErrors && $elementErrors != '') {
            $markup = $elementErrors . $markup;
        }

        if (! ($element instanceof Hidden) && $wrap) {

            if ($elementErrors != '') {
                $class = '';
            } else {
                $class = $element->getAttribute('data-container-class');
            }

            if (strpos($element->getAttribute('class'), 'visually-hidden') !== false) {
                $markup = sprintf(self::$format, 'visually-hidden', $markup);
            } elseif ($element->getOption('render-container') !== false) {

                $renderAsFieldset = $element->getOption('render_as_fieldset');

                if (!$renderAsFieldset) {
                    $markup = sprintf(self::$format, $class, $markup);
                }
            }
        }

        if ($oldRenderErrors && $elementErrors != '') {
            $markup = sprintf(self::$errorClass, $markup);
        }

        $this->setRenderErrors($oldRenderErrors);

        return $markup;
    }

    protected function renderFieldset(ElementInterface $element)
    {
        $label = $element->getLabel();
        $hint = sprintf(
            $this->fieldsetHintFormat,
            $this->getView()->translate(
                $element->getOption('hint')
            )
        );

        $element->setOption('hint', '');
        $element->setLabel('');
        $markup = $hint . $this->renderRow($element);

        if (!empty($label)) {

            $translator = $this->getTranslator();

            if ($translator !== null) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                $label = $escapeHtmlHelper($label);
            }

            $legend = sprintf(
                $this->fieldsetLabelWrapper,
                $label
            );
        }

        return sprintf(
            $this->fieldsetWrapper,
            $markup,
            $legend,
            '',
            ' class="fieldset--primary"'
        );
    }

    /**
     * Override the parent some more
     */
    protected function renderRow(ElementInterface $element)
    {
        $labelHelper         = $this->getLabelHelper();
        $elementHelper       = $this->getElementHelper();

        $label           = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }
        }

        // Does this element have errors ?
        if (count($element->getMessages()) > 0 && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        if ($this->partial) {
            $vars = array(
                'element'           => $element,
                'label'             => $label,
                'labelAttributes'   => $this->labelAttributes,
                'labelPosition'     => $this->labelPosition,
                'renderErrors'      => $this->renderErrors,
            );

            return $this->view->render($this->partial, $vars);
        }

        $elementString = $elementHelper->render($element);

        if (isset($label) && '' !== $label) {

            // @NOTE commented this out, we need to be able to add HTML to a label, can't see that commenting this out
            //  will affect anything
            //$label = $escapeHtmlHelper($label);
            $labelAttributes = $element->getLabelAttributes();

            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }

            // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
            // labels. The semantic way is to group them inside a fieldset
            $type = $element->getAttribute('type');
            if ($type === 'multi_checkbox' || $type === 'radio') {
                $fieldsetAttributes = $element->getOption('fieldset-attributes');
                $dataGroup = $element->getOption('fieldset-data-group');

                if (!is_null($dataGroup)) {
                    $fieldsetAttributes['data-group'] = $dataGroup;
                }

                $attributesString = '';

                if (is_array($fieldsetAttributes)) {
                    $attributesString = ' ' . $this->createAttributesString($fieldsetAttributes);
                }

                $markup = sprintf(
                    '<fieldset%s><legend>%s</legend>%s</fieldset>',
                    $attributesString,
                    $label,
                    $elementString
                );
            } else {
                if ($element->hasAttribute('id')
                    && ! ($element instanceof SingleCheckbox)
                    && ($element instanceof LabelAwareInterface && !$element->getLabelOption('always_wrap'))
                ) {
                    $labelOpen = '';
                    $labelClose = '';
                    $label = $labelHelper($element);
                } else {
                    $labelOpen  = $labelHelper->openTag($labelAttributes);
                    $labelClose = $labelHelper->closeTag();
                }

                if ($label !== '' && (!$element->hasAttribute('id'))
                    || ($element instanceof LabelAwareInterface && $element->getLabelOption('always_wrap'))
                ) {
                    $label = $label;
                }

                // Button element is a special case, because label is always rendered inside it
                if ($element instanceof Button) {
                    $labelOpen = $labelClose = $label = '';
                }

                $labelPosition = $this->labelPosition;
                if ($element instanceof LabelAwareInterface && $element->getLabelOption('label_position')) {
                    $labelPosition = $element->getLabelOption('label_position');
                }

                switch ($labelPosition) {
                    case self::LABEL_PREPEND:
                        $markup = $labelOpen . $label . $elementString . $labelClose;
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $markup = $labelOpen . $elementString . $label . $labelClose;
                }
            }

        } else {
            $markup = $elementString;
        }

        return $markup;
    }
}
