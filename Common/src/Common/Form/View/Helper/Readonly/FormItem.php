<?php

namespace Common\Form\View\Helper\Readonly;

use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;

/**
 * Class FormItem
 * @package Common\Form\View\Helper\Readonly
 */
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

    /**
     * @param ElementInterface $element
     * @return mixed
     */
    public function render(ElementInterface $element)
    {
        return $element->getValue();
    }
}
