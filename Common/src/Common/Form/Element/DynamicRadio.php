<?php

namespace Common\Form\Element;

use Zend\Form\Element\Radio;

/**
 * Class DynamicRadio
 * @package Common\Form\Element
 */
class DynamicRadio extends ErrorOverrideRadio
{
    use DynamicTrait;
}
