<?php

namespace Common\Form\Elements\Custom;

use Common\Form\Elements\Types\Radio;
use Common\Form\Elements\Types\RadioVertical;

class MultiCheckBoxContent extends RadioVertical
{
    /**
     * Get only the values from the options attribute
     *
     * @return array
     */
    protected function getValueOptionsValues()
    {
        $options = $this->getValueOptions();
        return array_keys($options);
    }
}
