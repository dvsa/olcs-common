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
    public function render(ZendFormInterface $form)
    {
        if (method_exists($form, 'prepare')) {
            $form->prepare();
        }

        $fieldsets = $elements = array();
        $hiddenSubmitElement = '';

        foreach ($form as $element) {

            if ($element instanceof FieldsetInterface) {
                $fieldsets[] = $this->getView()->formCollection($element);
            } elseif ($element->getName() == 'form-actions[submit]') {
                $hiddenSubmitElement = $this->getView()->formRow($element);
            } else {
                $elements[] = $this->getView()->formRow($element);
            }
        }

        return $this->openTag($form) .
                $hiddenSubmitElement .
                implode("\n", $fieldsets) .
                implode("\n", $elements) .
                $this->closeTag();
    }
}
