<?php

namespace Common\Form\View\Helper\Readonly;

use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;

class FormItem extends AbstractHelper
{
    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormElement
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    public function render(ElementInterface $element)
    {
        return $element->getValue();
    }
}