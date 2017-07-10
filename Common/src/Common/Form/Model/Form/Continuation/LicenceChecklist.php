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
     * @Form\Options({
     *     "hint": "continuations.checklist.form-hint",
     *     "hintClass": "form-hint",
     *     "label": "continuations.checklist.hidden.legend",
     *     "label_attributes": {"class": "visually-hidden"},
     *     "shouldWrap": true,
     *  })
     */
    public $data = null;
}
