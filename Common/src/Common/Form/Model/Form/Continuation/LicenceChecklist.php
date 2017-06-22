<?php

namespace Common\Form\Model\Form\Continuation;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class LicenceChecklist
{
    /**
     * @Form\Name("data")
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\LicenceChecklist")
     */
    public $data = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large"})
     * @Form\Options({"label":"Continue"})
     * @Form\Type("\Zend\Form\Element\Button")
     */
    public $submit = null;
}
