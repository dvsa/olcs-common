<?php

namespace Common\Form\Element;

use Zend\Form\Element\Select;

/**
 * Class DynamicSelect
 * @package Common\Form\Element
 */
class DynamicSelect extends Select
{
    use DynamicTrait;

    public function addValueOption(array $valueOption)
    {
        $this->setValueOptions(array_merge($this->getValueOptions(), $valueOption));
    }
}
