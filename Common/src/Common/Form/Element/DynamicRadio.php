<?php

namespace Common\Form\Element;

use Laminas\Form\Element\Radio;

/**
 * Class DynamicRadio
 * @package Common\Form\Element
 */
class DynamicRadio extends ErrorOverrideRadio
{
    use DynamicTrait;
}
