<?php

namespace Common\Form\Element;

use Zend\Form\Element\Radio;

/**
 * Class DynamicRadio
 * @package Common\Form\Element
 */
class DynamicRadio extends Radio
{
    const INPUT_CLASS_KEY = 'input_class';

    use DynamicTrait;

    public function getInputSpecification()
    {
        $spec = parent::getInputSpecification();

        if (isset($this->options[self::INPUT_CLASS_KEY])) {
            $spec['type'] = $this->options[self::INPUT_CLASS_KEY];
        }

        return $spec;
    }
}
