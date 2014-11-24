<?php

namespace Common\Form\Element;

use Zend\Form\Element\Select;

/**
 * Class DynamicMultiSelect
 * @package Common\Form\Element
 */
class DynamicMultiSelect extends Select
{
    use DynamicTrait;

    /**
     * Setup the element
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('multiple', 'multiple');
    }
}
