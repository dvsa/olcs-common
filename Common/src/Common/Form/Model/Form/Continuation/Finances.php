<?php

namespace Common\Form\Model\Form\Continuation;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class Finances
{
    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\Finances")
     */
    public $finances = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"Continue"})
     * @Form\Type("\Laminas\Form\Element\Button")
     */
    public $submit = null;
}
