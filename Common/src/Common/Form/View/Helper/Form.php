<?php

/**
 * Checkbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\FormInterface as ZendFormInterface;
use Zend\Form\FieldsetInterface;

/**
 * Checkbox element
 *
 * @author Someone <someone@valtech.co.uk>
 */
class Form extends \Zend\Form\View\Helper\Form
{
    /**
     * Render a form from the provided $form
     *  We override the parent here as we want to
     *   a. Add logging
     *   b. Ensure fieldsets come before elements
     *
     * @param  ZendFormInterface $form
     * @return string
     */
    public function render(ZendFormInterface $form, $includeFormTags = true)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $fieldsets = $elements = array();
        $hiddenSubmitElement = '';

        $this->getView()->formCollection()->setReadOnly($form->getOption('readonly'));
        $rowHelper = (
            $form->getOption('readonly') ?
            $this->getView()->plugin('readonlyformrow') :
            $this->getView()->plugin('formrow')
        );

        foreach ($form as $element) {

            if ($element instanceof FieldsetInterface) {
                $keepEmptyFieldset = $element->hasAttribute('keepEmptyFieldset') ?
                    $element->getAttribute('keepEmptyFieldset') : false;
                // do not display empty fieldsets as per OLCS-12318
                if (!$element->count() && !$keepEmptyFieldset) {
                    continue;
                }
                $fieldsets[] = $this->getView()->addTags(
                    $this->getView()->formCollection($element)
                );
            } elseif ($element->getName() == 'form-actions[continue]') {
                $hiddenSubmitElement = $rowHelper($element);
            } else {
                $elements[] = $rowHelper($element);
            }
        }

        return sprintf(
            '%s%s%s%s%s',
            $includeFormTags ? $this->openTag($form) : '',
            $hiddenSubmitElement,
            implode("\n", $fieldsets),
            implode("\n", $elements),
            $includeFormTags ? $this->closeTag() : ''
        );
    }
}
