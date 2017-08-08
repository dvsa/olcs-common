<?php

namespace Common\Form\Model\Form\Continuation;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ConditionsUndertakings
{
    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"continuations.conditions-undertakings.continue.label"})
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;
}
