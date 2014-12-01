<?php

namespace Common\Form\View\Helper\Readonly;

use Zend\Form\Element\Select;
use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper;

class FormSelect extends AbstractHelper
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

    public function processOptions($input)
    {
        $return = [];

        foreach ($input as $key => $options) {
            if (is_array($options)) {
                if (isset($options['options'])) {
                    $return = array_merge($return, $this->processOptions($options['options']));
                } else {
                    $return[$options['value']] = $options['label'];
                }
            } else {
                $return[$key] = $options;
            }
        }

        return $return;
    }

    public function render(ElementInterface $element)
    {
        if (!($element instanceof Select)) {
            return '';
        }

        $valueOptions = $this->processOptions($element->getValueOptions());

        if ($element->getAttribute('multiple')) {
            $labels = array_intersect_key($valueOptions, array_combine($element->getValue(), $element->getValue()));
            $value = implode(', ', $labels);
        } else {
            $value = $valueOptions[$element->getValue()];
        }

        return $value;
    }
}
