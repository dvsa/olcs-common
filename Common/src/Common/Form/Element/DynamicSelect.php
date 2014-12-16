<?php

namespace Common\Form\Element;

use Zend\Form\Element\Select;

/**
 * Class DynamicSelect
 * @package Common\Form\Element
 */
class DynamicSelect extends Select
{
    use DynamicTrait {
        DynamicTrait::getValueOptions as traitGetValueOptions;
    }

    /**
     * Returns the value options for this select, fetching from the refdata service if requried
     *
     * @return array
     */
    public function getValueOptions()
    {
        $this->valueOptions = $this->traitGetValueOptions();

        if (!is_null($this->emptyOption)) {
            $this->valueOptions[''] = $this->emptyOption;
        }

        return $this->valueOptions;
    }
}
