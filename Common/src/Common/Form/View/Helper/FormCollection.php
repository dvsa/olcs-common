<?php

/**
 * Form Collection wrapper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormCollection as ZendFormCollection;
use Zend\Form\ElementInterface;
use Common\Form\Elements\Types\PostcodeSearch;

/**
 * Form Collection wrapper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FormCollection extends ZendFormCollection
{
    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $markup = parent::render($element);

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
