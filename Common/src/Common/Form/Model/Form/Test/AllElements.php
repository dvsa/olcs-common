<?php

namespace Common\Form\Model\Form\Test;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("case")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class AllElements
{
    /**
     * @Form\Name("fields")
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TestAllElements")
     */
    public $fields;

    /**
     * @Form\Name("form-actions")
     * @Form\ComposedObject("\Common\Form\Model\Fieldset\SubmitButton")
     */
    public $formActions;
}
