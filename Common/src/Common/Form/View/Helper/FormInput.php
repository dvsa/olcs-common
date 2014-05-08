<?php

namespace Common\Form\View\Helper;

use Zend\Form\View\Helper\FormInput as ZendFormInput;
use Zend\Form\ElementInterface;
use Common\Form\Elements\Types\PostcodeSearch;

class FormInput extends ZendFormInput
{
    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        if ($element instanceof PostcodeSearch) {
            $renderer = $this->getView();
            $buttonRenderer = $renderer->plugin('form_button');
            return parent::render($element->getPostcodeElement()) . $buttonRenderer($element->getSearchButton());
        }

        return parent::render($element);
    }
}
