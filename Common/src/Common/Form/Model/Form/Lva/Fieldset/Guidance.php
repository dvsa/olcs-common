<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("guidance")
 */
class Guidance
{

    /**
     * @Form\Attributes({"value":"selfserve-app-subSection-your-business-people-guidance"})
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $guidance = null;
}
