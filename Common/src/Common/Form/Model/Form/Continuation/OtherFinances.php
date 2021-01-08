<?php

namespace Common\Form\Model\Form\Continuation;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class OtherFinances
{
    /**
     * @Form\Type("hidden")
     */
    public $version = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\OtherFinances")
     * @Form\Options({"label":"continuations.finances.otherFinances.label"})
     */
    public $finances = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"Continue"})
     * @Form\Type("\Laminas\Form\Element\Button")
     */
    public $submit = null;
}
